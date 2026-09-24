document.addEventListener( 'DOMContentLoaded', function () {
    var venster = document.getElementById( 'dvr-kaart-venster' );
    var schaal = document.getElementById( 'dvr-kaart-schaal' );
    if ( ! venster || ! schaal ) {
        return;
    }

    /* ================= Lagen aan/uit ================= */
    var toggles = document.querySelectorAll( '.laag-toggle' );
    toggles.forEach( function ( toggle ) {
        toggle.addEventListener( 'change', function () {
            var slug = toggle.getAttribute( 'data-laag' );
            var actief = toggle.checked;

            var overlay = schaal.querySelector( '.laag-overlay[data-laag="' + slug + '"]' );
            if ( overlay ) {
                overlay.classList.toggle( 'is-actief', actief );
            }

            schaal.querySelectorAll( '.infopunt-marker' ).forEach( function ( marker ) {
                var markerLagen = ( marker.getAttribute( 'data-laag' ) || '' ).split( ' ' );
                if ( markerLagen.indexOf( slug ) === -1 ) {
                    return;
                }
                marker.classList.toggle( 'is-actief', actief );
            } );
        } );
    } );

    /* ================= Modal met infopunt-details ================= */
    var modal = document.querySelector( '.infopunt-modal' );
    var modalTekst = modal ? modal.querySelector( '.infopunt-modal-tekst' ) : null;

    function modalOpenen( targetId ) {
        var bron = document.getElementById( targetId );
        if ( ! bron || ! modal || ! modalTekst ) {
            return;
        }
        modalTekst.innerHTML = bron.innerHTML;
        modal.hidden = false;
    }

    function modalSluiten() {
        if ( modal ) {
            modal.hidden = true;
        }
    }

    if ( modal ) {
        modal.querySelectorAll( '[data-modal-sluiten]' ).forEach( function ( el ) {
            el.addEventListener( 'click', modalSluiten );
        } );
        document.addEventListener( 'keydown', function ( event ) {
            if ( 'Escape' === event.key && ! modal.hidden ) {
                modalSluiten();
            }
        } );
    }

    /* ================= Pan & zoom ================= */
    var stand = { x: 0, y: 0, schaal: 1 };
    var MIN_SCHAAL = 1;
    var MAX_SCHAAL = 4;

    function toepassen() {
        schaal.style.transform = 'translate(' + stand.x + 'px, ' + stand.y + 'px) scale(' + stand.schaal + ')';
    }

    function begrenzen() {
        var vensterRect = venster.getBoundingClientRect();
        var maxX = vensterRect.width * ( stand.schaal - 1 );
        var maxY = vensterRect.height * ( stand.schaal - 1 );
        stand.x = Math.min( 0, Math.max( -maxX, stand.x ) );
        stand.y = Math.min( 0, Math.max( -maxY, stand.y ) );
    }

    function zoomenNaar( nieuweSchaal, pivotX, pivotY ) {
        nieuweSchaal = Math.min( MAX_SCHAAL, Math.max( MIN_SCHAAL, nieuweSchaal ) );
        var factor = nieuweSchaal / stand.schaal;
        stand.x = pivotX - ( pivotX - stand.x ) * factor;
        stand.y = pivotY - ( pivotY - stand.y ) * factor;
        stand.schaal = nieuweSchaal;
        begrenzen();
        toepassen();
    }

    venster.addEventListener( 'wheel', function ( event ) {
        event.preventDefault();
        var rect = venster.getBoundingClientRect();
        var pivotX = event.clientX - rect.left;
        var pivotY = event.clientY - rect.top;
        var delta = event.deltaY < 0 ? 1.15 : 1 / 1.15;
        zoomenNaar( stand.schaal * delta, pivotX, pivotY );
    }, { passive: false } );

    var slepen = false;
    var verplaatstAfstand = 0;
    var startPointer = { x: 0, y: 0 };
    var startStand = { x: 0, y: 0 };

    venster.addEventListener( 'pointerdown', function ( event ) {
        if ( event.target.closest( '.infopunt-marker' ) ) {
            return;
        }
        slepen = true;
        verplaatstAfstand = 0;
        startPointer.x = event.clientX;
        startPointer.y = event.clientY;
        startStand.x = stand.x;
        startStand.y = stand.y;
        venster.classList.add( 'is-slepen' );
        venster.setPointerCapture( event.pointerId );
    } );

    venster.addEventListener( 'pointermove', function ( event ) {
        if ( ! slepen ) {
            return;
        }
        var dx = event.clientX - startPointer.x;
        var dy = event.clientY - startPointer.y;
        verplaatstAfstand = Math.max( verplaatstAfstand, Math.abs( dx ), Math.abs( dy ) );
        stand.x = startStand.x + dx;
        stand.y = startStand.y + dy;
        begrenzen();
        toepassen();
    } );

    function slependStoppen( event ) {
        slepen = false;
        venster.classList.remove( 'is-slepen' );
        if ( event && event.pointerId !== undefined ) {
            try { venster.releasePointerCapture( event.pointerId ); } catch ( e ) {}
        }
    }
    venster.addEventListener( 'pointerup', slependStoppen );
    venster.addEventListener( 'pointercancel', slependStoppen );

    /* Klik op een marker onderdrukken als het eigenlijk een sleep-gebaar was */
    schaal.querySelectorAll( '.infopunt-marker' ).forEach( function ( marker ) {
        marker.addEventListener( 'click', function ( event ) {
            if ( verplaatstAfstand > 6 ) {
                event.preventDefault();
                event.stopPropagation();
                return;
            }
            modalOpenen( marker.getAttribute( 'data-target' ) );
        } );
    } );

    /* ================= Hover-tooltip met plaatsnaam ================= */
    var tooltip = document.getElementById( 'dvr-tooltip' );
    if ( tooltip ) {
        schaal.querySelectorAll( '.infopunt-marker' ).forEach( function ( marker ) {
            var naam = marker.getAttribute( 'aria-label' ) || '';
            marker.addEventListener( 'mouseenter', function ( event ) {
                tooltip.textContent = naam;
                tooltip.style.left = event.clientX + 'px';
                tooltip.style.top = event.clientY + 'px';
                tooltip.hidden = false;
            } );
            marker.addEventListener( 'mousemove', function ( event ) {
                tooltip.style.left = event.clientX + 'px';
                tooltip.style.top = event.clientY + 'px';
            } );
            marker.addEventListener( 'mouseleave', function () {
                tooltip.hidden = true;
            } );
        } );
    }

    /* ================= Zoekveld ================= */
    var zoekveld = document.getElementById( 'dvr-zoekveld' );
    var resultatenLijst = document.getElementById( 'dvr-zoek-resultaten' );
    if ( zoekveld && resultatenLijst ) {
        var alleMarkers = Array.prototype.slice.call( schaal.querySelectorAll( '.infopunt-marker' ) );

        function springNaar( marker ) {
            marker.getAttribute( 'data-laag' ).split( ' ' ).forEach( function ( slug ) {
                if ( ! slug ) {
                    return;
                }
                var toggle = document.querySelector( '.laag-toggle[data-laag="' + slug + '"]' );
                if ( toggle && ! toggle.checked ) {
                    toggle.checked = true;
                    toggle.dispatchEvent( new Event( 'change' ) );
                }
            } );

            var vensterRect = venster.getBoundingClientRect();
            var links = parseFloat( marker.style.left ) / 100 * vensterRect.width;
            var boven = parseFloat( marker.style.top ) / 100 * vensterRect.height;
            stand.schaal = 2;
            stand.x = vensterRect.width / 2 - links * stand.schaal;
            stand.y = vensterRect.height / 2 - boven * stand.schaal;
            begrenzen();
            toepassen();

            resultatenLijst.hidden = true;
            zoekveld.value = marker.getAttribute( 'aria-label' ) || '';
            modalOpenen( marker.getAttribute( 'data-target' ) );
        }

        zoekveld.addEventListener( 'input', function () {
            var term = zoekveld.value.trim().toLowerCase();
            resultatenLijst.innerHTML = '';
            if ( term.length < 2 ) {
                resultatenLijst.hidden = true;
                return;
            }
            var treffers = alleMarkers.filter( function ( marker ) {
                var naam = ( marker.getAttribute( 'aria-label' ) || '' ).toLowerCase();
                return naam.indexOf( term ) !== -1;
            } ).slice( 0, 8 );

            if ( ! treffers.length ) {
                resultatenLijst.hidden = true;
                return;
            }

            treffers.forEach( function ( marker ) {
                var knop = document.createElement( 'button' );
                knop.type = 'button';
                knop.textContent = marker.getAttribute( 'aria-label' );
                knop.addEventListener( 'click', function () {
                    springNaar( marker );
                } );
                resultatenLijst.appendChild( knop );
            } );
            resultatenLijst.hidden = false;
        } );
    }
} );
