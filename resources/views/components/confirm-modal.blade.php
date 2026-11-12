<dialog id="confirm-modal" class="m-auto w-full max-w-md rounded-xl p-0 shadow-xl"
        aria-labelledby="confirm-title" aria-describedby="confirm-message">
    <form method="dialog" class="p-6">
        <div class="flex items-start gap-4">
            <div id="confirm-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h3 id="confirm-title" class="text-base font-semibold text-slate-900"></h3>
                <p id="confirm-message" class="mt-2 text-sm text-slate-600 whitespace-pre-line"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="submit" value="cancel" id="confirm-cancel" autofocus
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors"></button>
            <button type="submit" value="ok" id="confirm-ok"></button>
        </div>
    </form>
</dialog>

<style>#confirm-modal::backdrop { background: rgba(15, 23, 42, .5); }</style>

<script>
    (() => {
        const dlg = document.getElementById('confirm-modal');
        if (!dlg) return;
        const $ = (id) => document.getElementById(id);

        const estilos = {
            danger:  { icono: 'bg-red-100 text-red-600',   ok: 'bg-red-600 hover:bg-red-700' },
            primary: { icono: 'bg-blue-100 text-blue-600', ok: 'bg-blue-600 hover:bg-blue-700' },
        };

        // Uso desde cualquier script: if (!(await confirmarAccion('¿Seguro?'))) return;
        window.confirmarAccion = (mensaje, { titulo = 'Confirmar acción', aceptar = 'Aceptar', cancelar = 'Cancelar', variante = 'primary' } = {}) =>
            new Promise((resolve) => {
                if (dlg.open) return resolve(false);
                const est = estilos[variante] || estilos.primary;

                $('confirm-title').textContent = titulo;
                $('confirm-message').textContent = mensaje;
                $('confirm-cancel').textContent = cancelar;
                $('confirm-ok').textContent = aceptar;
                $('confirm-icon').className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full ' + est.icono;
                $('confirm-ok').className = 'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors ' + est.ok;

                dlg.returnValue = '';
                dlg.addEventListener('close', () => resolve(dlg.returnValue === 'ok'), { once: true });
                dlg.showModal();
            });

        // Clic fuera del cuadro = cancelar
        dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close('cancel'); });

        function opciones(origen, esDelete) {
            const d = origen.dataset;
            return {
                titulo: d.confirmTitulo || (esDelete ? 'Eliminar registro' : undefined),
                aceptar: d.confirmAceptar || (esDelete ? 'Eliminar' : undefined),
                variante: d.confirmVariante || (esDelete ? 'danger' : 'primary'),
            };
        }

        // Formularios (o su botón de envío) con data-confirm
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement) || form.dataset.confirmado) return;

            const origen = e.submitter?.dataset.confirm ? e.submitter : form;
            if (!origen.dataset.confirm) return;

            e.preventDefault();
            const esDelete = form.querySelector('input[name="_method"]')?.value.toUpperCase() === 'DELETE';
            if (!(await window.confirmarAccion(origen.dataset.confirm, opciones(origen, esDelete)))) return;

            form.dataset.confirmado = '1';
            form.requestSubmit(e.submitter);
            delete form.dataset.confirmado;
        });

        // Enlaces con data-confirm
        document.addEventListener('click', async (e) => {
            const a = e.target.closest('a[data-confirm]');
            if (!a) return;
            e.preventDefault();
            if (await window.confirmarAccion(a.dataset.confirm, opciones(a, false))) window.location.href = a.href;
        });
    })();
</script>
