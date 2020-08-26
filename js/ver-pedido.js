// Ver pedido.
var i = 0;
var datosPedido, datosSeries, obtenerColor;

$('#verPedido').on('show.bs.modal', function (e) {

    let pedidoId = $(e.relatedTarget).data('id');
    $('.contenedorPedidos').empty();

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerPedidoId',
        data: 'pedido_id=' + pedidoId,
        async: false,
        success: function (data) {

            datosPedido = JSON.parse(data);

            datosSeries = datosPedido.filter((pedido, index, self) =>
                index === self.findIndex((elem) => (
                    elem.SERIE_ID === pedido.SERIE_ID && elem.COLOR_ID === pedido.COLOR_ID
                ))
            );

        }
    });

    // Obtenemos el grupo de Series para popular la tabla.
    datosSeries.forEach(serie => {

        let serieId = serie.SERIE_ID;
        let colorId = serie.COLOR_ID;

        $.ajax({
        type: 'get',
        url: `backend/api/utils.php?fun=obtenerGrupoSerie&id=${serieId}`,
        async: false,
        success: function (data) {

            const result = JSON.parse(data);

            // Obtenemos el respectivo "color" dependiendo del ID de la serie.
            $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerColor',
            async: false,
            data: `id=${colorId}`,
            success: function (data) {

                obtenerColor = JSON.parse(data);

                }
                
            });

            // Decoración del color en cada serie.
            let color = obtenerColor[0].COLOR;
            let backgroundHex = obtenerColor[0].CODIGO;

            let red = parseInt(backgroundHex.substring(1, 3), 16);
            let green = parseInt(backgroundHex.substring(3, 5), 16);
            let blue = parseInt(backgroundHex.substring(5, 7), 16);
            
            let colorHex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

            $('.contenedorPedidos').append(`
                <div id="serie-${i}" class="contenedor-serie shadow-sm">
                    <div class="form-row">
                        <div class="col">
                            <strong>${result[0].MARCA.toProperCase()}</strong>
                            <span class="badge border" style="background-color: ${backgroundHex}; color: ${colorHex};">${color.toProperCase()}</span>
                            <small class="text-muted">${result[0].TALLA} al ${result[result.length - 1].TALLA}</small>
                        </div>
                    </div>
                    <div id="grupoSeries-${i}" class="form-row text-center">
                    </div>
                </div>        
            `);

            result.forEach(row => {

                $('#grupoSeries-' + i).append(`
                    <div class="form-group col mb-0 mt-2">
                        <label class="label-cantidades" for="cantidades">${row.TALLA}</label>
                        <input class="form-control input-cantidades" data-suela-id="${row.SUELA_ID}" data-color-id="${colorId}" type="number" value="0" readonly>
                    </div>
                `);

            });

            i++;

        }

        });

    });

    // Agregamos las cantidades.
    $('.input-cantidades').each(function() {

        datosPedido.some(elem => {

            if (elem.SUELA_ID == $(this).data('suela-id') && elem.COLOR_ID == $(this).data('color-id')) {

               return this.value = elem.CANTIDAD;

            }

        });

    });

});