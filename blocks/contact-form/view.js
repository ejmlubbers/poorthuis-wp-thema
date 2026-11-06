/* Formulier versturen zonder paginaherlaad; valt terug op gewone browsercontrole. */
( function () {
	document.querySelectorAll( 'form.ph-form' ).forEach( ( form ) => {
		const status = form.querySelector( '.ph-form-status' );
		const btn = form.querySelector( 'button[type=submit]' );
		const label = btn.innerHTML;

		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			if ( ! form.reportValidity() ) return;
			const fd = new FormData( form );
			fd.append( '_page', location.href );
			btn.disabled = true;
			btn.textContent = form.dataset.sending;
			status.className = 'ph-form-status';
			status.textContent = '';
			try {
				const res = await fetch( form.action, { method: 'POST', body: fd } );
				const json = await res.json().catch( () => ( {} ) );
				if ( ! res.ok || ! json.ok ) throw new Error( json.message || form.dataset.error );
				form.reset();
				status.classList.add( 'is-ok' );
				status.textContent = form.dataset.ok;
				btn.innerHTML = '✓';
			} catch ( err ) {
				status.classList.add( 'is-error' );
				status.textContent = err.message;
				btn.disabled = false;
				btn.innerHTML = label;
			}
		} );
	} );
} )();
