/**
 * Navigatie tussen de kaart en de subpagina's als overlay-kader bovenop de
 * kaart, zonder dat de kaart ooit opnieuw geladen wordt. Klikken op een
 * menu-item haalt de inhoud op via de REST API en toont die in het kader;
 * de URL wordt bijgewerkt met history.pushState zodat de terugknop werkt en
 * een directe link naar een subpagina de kaart laadt met het kader al open
 * (dat laatste doet de server al, zie functions.php).
 */
( function () {
    'use strict';

    var data     = window.dvrOverlayData || {};
    var menu     = Array.isArray( data.menu ) ? data.menu : [];
    var restBase = data.restBase || '';
    var homeUrl  = data.homeUrl || '/';

    var overlay = document.getElementById( 'dvr-overlay' );
    var inhoud  = document.getElementById( 'dvr-overlay-inhoud' );
    if ( ! overlay || ! inhoud ) {
        return;
    }

    function normaliseer( url ) {
        try {
            var u = new URL( url, window.location.origin );
            return u.origin + u.pathname + u.search;
        } catch ( e ) {
            return url;
        }
    }

    function escapeHtml( tekst ) {
        var div = document.createElement( 'div' );
        div.textContent = tekst || '';
        return div.innerHTML;
    }

    var homeGenormaliseerd = normaliseer( homeUrl );
    var slugPerUrl = {};
    var urlPerSlug = {};

    menu.forEach( function ( item ) {
        if ( ! item.slug || normaliseer( item.url ) === homeGenormaliseerd ) {
            return;
        }
        slugPerUrl[ normaliseer( item.url ) ] = item.slug;
        urlPerSlug[ item.slug ] = item.url;
    } );

    function vindSlug( href ) {
        return slugPerUrl[ normaliseer( href ) ] || null;
    }

    function isHome( href ) {
        return normaliseer( href ) === homeGenormaliseerd;
    }

    var oorspronkelijkeTitel = data.homeTitel || document.title;
    var cache = {};
    var huidigeSlug = null;

    function overlayTonen( slug, fragment ) {
        huidigeSlug = slug;
        inhoud.innerHTML = '<h1>' + escapeHtml( fragment.title ) + '</h1>' + fragment.html;
        document.title = fragment.title ? fragment.title + ' – De Droomvrouwroute' : oorspronkelijkeTitel;
        overlay.classList.add( 'is-open' );
        document.body.classList.add( 'dvr-overlay-open' );
        inhoud.scrollTop = 0;
        var sluitKnop = overlay.querySelector( '.dvr-overlay-sluiten' );
        if ( sluitKnop ) {
            sluitKnop.focus();
        }
        /* Instagram-embed.js verwerkt alleen blockquotes die al in de pagina
           stonden toen het script laadde; na een AJAX-wissel moet het
           opnieuw gevraagd worden de nieuwe inhoud te verwerken. */
        if ( window.instgrm && window.instgrm.Embeds ) {
            window.instgrm.Embeds.process();
        }
    }

    function overlaySluiten() {
        huidigeSlug = null;
        overlay.classList.remove( 'is-open' );
        document.body.classList.remove( 'dvr-overlay-open' );
        document.title = oorspronkelijkeTitel;
    }

    function fragmentOphalen( slug ) {
        if ( cache[ slug ] ) {
            return Promise.resolve( cache[ slug ] );
        }
        return fetch( restBase + encodeURIComponent( slug ), { credentials: 'same-origin' } )
            .then( function ( resp ) {
                if ( ! resp.ok ) {
                    throw new Error( 'HTTP ' + resp.status );
                }
                return resp.json();
            } )
            .then( function ( json ) {
                cache[ slug ] = json;
                return json;
            } );
    }

    /* Teller tegen race-condities: als iemand snel na elkaar twee keer
       klikt (of meteen weer sluit) terwijl de eerste fetch nog loopt, mag
       die eerste, inmiddels verouderde, respons de nieuwere actie niet
       meer overschrijven. Elke navigatie-actie (naar een slug, of naar
       home) verhoogt de teller; een fetch past zijn resultaat alleen toe
       als de teller nog hetzelfde is als bij het starten. */
    var navigatieTeller = 0;

    function naarSlug( slug, zetGeschiedenis ) {
        var teller = ++navigatieTeller;
        overlay.classList.add( 'is-open' );
        document.body.classList.add( 'dvr-overlay-open' );
        if ( ! cache[ slug ] ) {
            inhoud.innerHTML = '<p class="dvr-overlay-laden">Laden…</p>';
        }
        fragmentOphalen( slug ).then( function ( fragment ) {
            if ( teller !== navigatieTeller ) {
                return;
            }
            overlayTonen( slug, fragment );
            if ( zetGeschiedenis ) {
                window.history.pushState( { dvrSlug: slug }, '', urlPerSlug[ slug ] || homeUrl );
            }
        } ).catch( function () {
            if ( teller !== navigatieTeller ) {
                return;
            }
            inhoud.innerHTML = '<p class="dvr-overlay-laden">Kon de inhoud niet laden. Probeer het opnieuw.</p>';
        } );
    }

    function naarHome( zetGeschiedenis ) {
        navigatieTeller++;
        var wasOpen = overlay.classList.contains( 'is-open' );
        overlaySluiten();
        if ( zetGeschiedenis && wasOpen ) {
            window.history.pushState( { dvrSlug: null }, '', homeUrl );
        }
    }

    /* Eén klik-handler voor het hele document: menu-links (in het
       "Menu"-paneel op de kaart, of eventueel binnen het kader zelf)
       onderscheppen en via AJAX laden; een klik daarbuiten terwijl het
       kader open is, sluit het (de kaart blijft gewoon zichtbaar). */
    document.addEventListener( 'click', function ( event ) {
        var link = event.target.closest( 'a[href]' );
        if ( link ) {
            if ( isHome( link.href ) ) {
                event.preventDefault();
                naarHome( true );
                return;
            }
            var slug = vindSlug( link.href );
            if ( slug ) {
                event.preventDefault();
                if ( slug !== huidigeSlug ) {
                    naarSlug( slug, true );
                }
            }
            return;
        }

        if (
            overlay.classList.contains( 'is-open' ) &&
            ! event.target.closest( '.dvr-overlay-paneel' ) &&
            ! event.target.closest( '.dvr-lichtbak' )
        ) {
            naarHome( true );
        }
    } );

    overlay.querySelectorAll( '[data-dvr-overlay-sluiten]' ).forEach( function ( el ) {
        el.addEventListener( 'click', function () {
            naarHome( true );
        } );
    } );

    document.addEventListener( 'keydown', function ( event ) {
        if ( 'Escape' === event.key && overlay.classList.contains( 'is-open' ) ) {
            naarHome( true );
        }
    } );

    window.addEventListener( 'popstate', function ( event ) {
        var slug = event.state && 'dvrSlug' in event.state ? event.state.dvrSlug : vindSlug( window.location.href );
        if ( slug ) {
            if ( cache[ slug ] ) {
                overlayTonen( slug, cache[ slug ] );
            } else {
                naarSlug( slug, false );
            }
        } else {
            overlaySluiten();
        }
    } );

    /* Lichtbak voor de Kunst-galerij: foto vergroten in de pagina zelf i.p.v.
       in een nieuw tabblad. Werkt via klik-delegatie, dus ook voor
       galerij-inhoud die pas later via AJAX wordt ingeladen. */
    var lichtbak = null;

    function lichtbakTonen( url, alt ) {
        if ( ! lichtbak ) {
            lichtbak = document.createElement( 'div' );
            lichtbak.className = 'dvr-lichtbak';
            lichtbak.innerHTML = '<button type="button" class="dvr-lichtbak-sluiten" aria-label="Sluiten">&times;</button><img class="dvr-lichtbak-afbeelding" alt="">';
            document.body.appendChild( lichtbak );
            lichtbak.addEventListener( 'click', function ( event ) {
                if ( event.target === lichtbak || event.target.closest( '.dvr-lichtbak-sluiten' ) ) {
                    lichtbakSluiten();
                }
            } );
        }
        lichtbak.querySelector( '.dvr-lichtbak-afbeelding' ).src = url;
        lichtbak.querySelector( '.dvr-lichtbak-afbeelding' ).alt = alt || '';
        lichtbak.classList.add( 'is-open' );
    }

    function lichtbakSluiten() {
        if ( lichtbak ) {
            lichtbak.classList.remove( 'is-open' );
        }
    }

    document.addEventListener( 'click', function ( event ) {
        var galerijLink = event.target.closest( '.dvr-galerij-link' );
        if ( ! galerijLink ) {
            return;
        }
        event.preventDefault();
        var img = galerijLink.querySelector( 'img' );
        lichtbakTonen( galerijLink.href, img ? img.alt : '' );
    } );

    document.addEventListener( 'keydown', function ( event ) {
        if ( 'Escape' === event.key && lichtbak && lichtbak.classList.contains( 'is-open' ) ) {
            lichtbakSluiten();
        }
    } );

    /* Beginstatus: de server kan het kader al open hebben gerenderd (direct
       bezocht via een subpagina-URL) — cache die inhoud en zet een eigen
       geschiedenis-item, zonder opnieuw te fetchen of de URL te wijzigen. */
    if ( overlay.classList.contains( 'is-open' ) ) {
        var beginSlug = vindSlug( window.location.href );
        huidigeSlug = beginSlug;
        if ( beginSlug ) {
            cache[ beginSlug ] = {
                title: document.title.replace( / – .*$/, '' ),
                html: inhoud.innerHTML.replace( /^<h1>.*?<\/h1>/, '' ),
            };
        }
        window.history.replaceState( { dvrSlug: beginSlug }, '', window.location.href );
    } else {
        window.history.replaceState( { dvrSlug: null }, '', window.location.href );
    }
} )();
