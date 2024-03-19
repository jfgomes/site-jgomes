<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!--- Basic Page Needs ================================================== -->
    <title>Portugal location caches </title>
    @include('partials.meta')

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <!-- Favicons ================================================== -->
    <link rel="shortcut icon" href="favicon.png" >

    <!-- JS + CSS ================================================== -->
    @include('partials.css_js')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Incluir Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* Estilo para definir a altura do mapa */
        #map {
            height: 88vh; /* Ajuste conforme necessário */
        }

        /* Estilo para definir a altura do textarea */
        #loadLogs {
            height: 88vh; /* Ajuste conforme necessário */
            padding:5px; width: 100%; position: relative; z-index: 3;
            background-color: #0c5460;
            color: white;
            border: 1px solid white;
        }

        /* Estilo para o contêiner principal */
        section {
            display: flex;
            flex-direction: row;
            height: 90vh; /* Ajuste conforme necessário */
        }

        /* Estilo para o contêiner da coluna esquerda */
        section > div:first-child {
            flex: 2;
        }

        /* Estilo para o contêiner da coluna direita */
        section > div:last-child {
            flex: 1;
        }

        .custom-btn {
            color: #fff; /* Cor do texto */
            background-color: dodgerblue; /* Cor de fundo */
            /*      border: 1px solid #ccc;  Borda */
            border: none;
            padding: 5px 10px; /* Espaçamento interno */
            margin-bottom: 10px; /* Margem inferior */
            cursor: pointer; /* Cursor */
            transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Transição suave */
        }

        .custom-btn:hover {
            background-color: #ddd; /* Cor de fundo ao passar o mouse */
            color: #333; /* Cor do texto ao passar o mouse */
            border-color: #bbb; /* Cor da borda ao passar o mouse */
        }
    </style>
</head>
<body>
<!-- Overlay to block the page during the loading ================================================== -->
@include('partials.overlay')

<!-- Header ================================================== -->
<header>
    <div class="header-content">
        <h1>🇵🇹 Location caches</h1>
        <div class="button-container">
            <a href="/home"><button class="adminBtn">🏠 Home</button></a>
            <a href="/admin" class="adminLink"><button class="adminBtn">👮‍♀️ Admin</button></a>
            @include('partials.logout')
        </div>
    </div>
</header> <!-- Header End -->

<!-- Conteúdo da sessão ================================================== -->
<div style="height: 55px">
    <!-- Coloque aqui o conteúdo da sua sessão -->
</div>

<!-- Mapa ================================================== -->
<section style="height: 400px;">
    <div style="display: flex;">
        <div id="testx" style="flex: 2;">
            <div style="position: relative; z-index: 2;">
                <select id="districtSelect" class="backInLeft custom-btn" style="display: none;"></select>
                <select id="municipalitySelect" class=" custom-btn" style="display: none"></select>
                <select id="parishSelect" class=" custom-btn" style="display: none"></select>
            </div>
            <div  id="map"   ></div>
        </div>
        <div  style="flex: 1; display: flex; flex-direction: column;">
            <div class="backInRight" style="position: relative; z-index: 2;display: none;margin-left: 1%">
                <button id="resetRedisCache" class="custom-btn">Reset Redis</button>
                <button id="resetAPCuCache" class="custom-btn">Reset APCu</button>
            </div>
            <div style="flex: 1;margin-left: 1%">
                <textarea id="loadLogs" class="backInRight" style="width: 99%;display: none;" readonly></textarea>
            </div>
        </div>
    </div>


    <script>



        $('#resetRedisCache').on('click', function() {
            $.ajax({
                url: '/reset_redis_cache_for_locations', // Substitua '/reset-all-caches' pela sua rota correspondente no Laravel
                type: 'GET', // Ou 'GET', dependendo do método que você está usando no Laravel
                success: function(response) {
                    // Lógica para lidar com a resposta, se necessário
                   // alert('Todos os caches foram resetados com sucesso.');
                    $("#loadLogs").val($('#loadLogs').val() + "All Redis cache was cleaned! \n\n")
                        .addClass("animate__animated animate__headShake");

                    setTimeout(function() {
                        $(".backInRight").removeClass("animate__animated animate__headShake");
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    // Lógica para lidar com erros, se necessário
                    alert('Ocorreu um erro ao tentar redefinir todos os caches.');
                }
            });
        });

        $('#resetAPCuCache').on('click', function() {
            $.ajax({
                url: '/reset_apcu_cache_for_locations', // Substitua '/reset-all-caches' pela sua rota correspondente no Laravel
                type: 'GET', // Ou 'GET', dependendo do método que você está usando no Laravel
                success: function(response) {
                    // Lógica para lidar com a resposta, se necessário
                    //alert('Todos os caches foram resetados com sucesso.');
                    $("#loadLogs").val($('#loadLogs').val() + "APCu cache for '{{ url()->to('/') }}' was cleaned! \n\n")
                        .addClass("animate__animated animate__headShake");

                    setTimeout(function() {
                        $(".backInRight").removeClass("animate__animated animate__headShake");
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    // Lógica para lidar com erros, se necessário
                    alert('Ocorreu um erro ao tentar redefinir todos os caches.');
                }
            });
        });

        function getCoordsByLocationString(addressComingFromInput, zoom)
        {
            // incoming user address from input should be encoded to be used in url
            const encodedAddress = encodeURIComponent("Portugal, " + addressComingFromInput);
            const nominatimURL = 'https://nominatim.openstreetmap.org/search?addressDetails=1&q=' + encodedAddress + '&format=json&limit=1';

            // fetch lat and long and use it with leaflet
            fetch(nominatimURL)
                .then(response => response.json())
                .then(data => {
                    const lat = data[0].lat;
                    const long = data[0].lon;
                    console.log(lat, long)
                    if (mymap != undefined) {
                        mymap.remove();
                    }

                    mymap = L.map('map').setView([lat, long], zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: ''
                    }).addTo(mymap);
                });
        }

        let mymap = L.map('map').setView([39.557191, -7.8536599], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(mymap);

    </script>
</section>


<!-- Footer ================================================== -->
<footer>
    @include('partials.cookies')
</footer> <!-- Footer End-->

<!-- Get data ================================================== -->
<script>

    // Função para construir o URL com base nos parâmetros e valores fornecidos
    function buildUrl(baseUrl, params) {
        let url = baseUrl + '?';
        Object.entries(params).forEach(([key, value], index, array) => {
            url += `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
            if (index !== array.length - 1) {
                url += '&';
            }
        });
        return url;
    }

    // Objeto contendo os parâmetros
    let params = {
        level: "districts"
    };



    // Set districts
      serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/api-map-caches', params)).then(response => {



          // Limpe o campo de seleção, caso já haja opções
          $('#districtSelect').empty();
          $('#municipalitySelect').empty();
          $('#parishSelect').empty();

          // Adiciona a opção padrão
          $('#districtSelect').append($('<option>', {
              value: "",
              text: "Select a district"
          }));




          // Itere sobre os resultados e adicione opções ao campo de seleção
          $.each(response.result.locations, function(index, district) {

              let districtObj = JSON.parse(district);

              $('#districtSelect').append($('<option>', {
                  value: districtObj.district_code,
                  text: districtObj.district_name
              }));

          });

          //$('.select2').select2();


              $(".backInRight").show()
                  .addClass("animate__animated animate__backInRight");

          setTimeout(function() {
              $(".backInRight").removeClass("animate__animated animate__backInRight");
          }, 1000);

              $(".backInLeft").show()
                  .addClass("animate__animated animate__backInLeft");

          setTimeout(function() {
              $(".backInLeft").removeClass("animate__animated animate__backInLeft");
          }, 1000);

          setTimeout(function() {
              $("#overlay").hide();
              $("#loadLogs").val("Districts list load from: '" + response.result.source + "'\n\n")
                  .scrollTop($("#loadLogs")[0].scrollHeight);
          }, 500);


      });

      // Set municipality

    // Manipulador de eventos para o select de distritos
    $('#districtSelect').on('change', function() {
        // Obtém o código do distrito selecionado
        let selectedDistrictCode = $(this).val();
        // Se não foi selecionado nenhum distrito, limpa o select de municípios
        if (!selectedDistrictCode) {
            $('#municipalitySelect').empty();
            return;
        }

        var textoSelecionado = $(this).find('option:selected').text();
        getCoordsByLocationString(textoSelecionado, 10);

        // Constrói os novos parâmetros para obter os municípios do distrito selecionado
        let districtParams = {
            level: "municipality",
            options: selectedDistrictCode
        };

        // Faz a solicitação para obter os municípios do distrito selecionado
        serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/api-map-caches', districtParams))
            .then(response => {
                // Limpa o campo de seleção, caso já haja opções
                $('#municipalitySelect').empty();
                $('#parishSelect').empty();

                // Adiciona a opção padrão
                $('#municipalitySelect').append($('<option>', {
                    value: "",
                    text: "Selecione um município"
                }));
                // Itera sobre os resultados e adiciona opções ao campo de seleção
                $.each(response.result.locations, function(index, municipality) {
                    let municipalityObj = JSON.parse(municipality);
                    $('#municipalitySelect').append($('<option>', {
                        value: municipalityObj.municipality_code,
                        text: municipalityObj.municipality_name
                    }));
                });

                $('#municipalitySelect').show(); //.addClass("animate__animated animate__backInLeft");
                //$(".backInLeft").show().addClass("animate__animated animate__backInLeft");
                $("#parishSelect").hide();
                $("#loadLogs").val($('#loadLogs').val() + "Municipality list for '" + textoSelecionado + "' load from: '" + response.result.source + "'\n\n")
                    .scrollTop($("#loadLogs")[0].scrollHeight)
                    .addClass("animate__animated animate__headShake");
                setTimeout(function() {
                    $(".backInRight").removeClass("animate__animated animate__headShake");
                }, 1000);

//
            })
            .catch(error => {
                console.error('Erro ao obter municípios:', error);
            }).finally(() => {
            // Hide the overlay regardless of success or failure
            setTimeout(function() {
                $("#overlay").hide();
            }, 100); // 1000 milissegundos = 1 segundo

        });
    });

    // Set parish

    // Manipulador de eventos para o select de mun
    $('#municipalitySelect').on('change', function() {
        // Obtém o código do mun selecionado
        let selectedMunicipalityCode = $(this).val();
        // Se não foi selecionado nenhum mun, limpa o select de pari
        if (!selectedMunicipalityCode) {
            $('#municipalitySelect').empty();
            return;
        }
        // Constrói os novos parâmetros para obter os parish do mun selecionado
        let municipalityParams = {
            level: "parish",
            options: selectedMunicipalityCode
        };

        var textoSelecionado = $(this).find('option:selected').text();
        getCoordsByLocationString(textoSelecionado, 13);

        // Faz a solicitação para obter os municípios do distrito selecionado
        serverLessRequests.checkAuthAndGetData(buildUrl('/api/v1/api-map-caches', municipalityParams))
            .then(response => {
                // Limpa o campo de seleção, caso já haja opções
                $('#parishSelect').empty();
                // Adiciona a opção padrão
                $('#parishSelect').append($('<option>', {
                    value: "",
                    text: "Selecione uma freguesia"
                }));
                // Itera sobre os resultados e adiciona opções ao campo de seleção
                $.each(response.result.locations, function(index, parish) {
                    let parishObj = JSON.parse(parish);
                    $('#parishSelect').append($('<option>', {
                        value: parishObj.parish_code,
                        text: parishObj.parish_name
                    }));
                });

                $('#parishSelect').show().addClass("animate__animated animate__backInLeft");
                $("#loadLogs").val($('#loadLogs').val() + "Parish list for '" + textoSelecionado + "' load from: '" + response.result.source + "'\n\n")
                    .scrollTop($("#loadLogs")[0].scrollHeight)
                    .addClass("animate__animated animate__headShake");

                setTimeout(function() {
                    $(".backInRight").removeClass("animate__animated animate__headShake");
                }, 1000);
            })
            .catch(error => {
                console.error('Erro ao obter parish:', error);
            }).finally(() => {
            // Hide the overlay regardless of success or failure
            setTimeout(function() {
                $("#overlay").hide();
            }, 100); // 1000 milissegundos = 1 segundo

        });
    });




    $('#parishSelect').on('change', function() {


        var textoSelecionado = $(this).find('option:selected').text();
        getCoordsByLocationString(textoSelecionado, 15);

        $("#loadLogs").val($('#loadLogs').val() + "Parish '" + textoSelecionado + "' selected!\n\n")
            .scrollTop($("#loadLogs")[0].scrollHeight)
            .addClass("animate__animated animate__headShake");

        setTimeout(function() {
            $(".backInRight").removeClass("animate__animated animate__headShake");
        }, 1000);

    });

</script>
</body>
</html>
