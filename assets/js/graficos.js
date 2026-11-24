// Gráficos de barras con datos obtenidos desde MySQL.
document.querySelectorAll('[data-grafico]').forEach(grafico => {
    const datos = JSON.parse(grafico.dataset.grafico);
    const filas = Array.isArray(datos) ? datos : Object.entries(datos).map(([nombre,total]) => ({nombre,total}));
    const maximo = Math.max(1, ...filas.map(f => Number(f.total)));
    if (!filas.length) grafico.textContent = 'No hay datos disponibles.';
    filas.forEach(f => {
        const fila = document.createElement('div'); fila.className = 'barra-fila';
        const nombre = document.createElement('span'); nombre.textContent = f.nombre;
        const pista = document.createElement('div'); pista.className = 'barra-pista';
        const barra = document.createElement('div'); barra.className = 'barra-valor'; barra.style.width = (100 * f.total / maximo) + '%';
        const valor = document.createElement('strong'); valor.textContent = f.total;
        pista.append(barra); fila.append(nombre,pista,valor); grafico.append(fila);
    });
});
