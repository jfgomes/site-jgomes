<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        div.container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        div.section {
            margin-bottom: 20px;
        }

        p.category {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        div.sub-section {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        div.sub-section > div {
            flex: 1 1 calc(48% - 10px);
            margin: 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        p.service-name {
            font-size: 16px;
            color: #555;
            margin: 0;
        }

        a.link {
            text-decoration: none;
            color: #007bff;
        }

        div.sub-section div div {
            justify-content: space-between;
        }

        button.more-info-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 8px;
            border-radius: 4px;
            cursor: pointer;
        }

        div.popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        div.popup p {
            white-space: nowrap;
            overflow: auto;
        }

        .info-popup {
            padding-bottom: 15px;
        }
        .info-popup strong {
            margin-left: -2px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 style="text-align: center">Services</h1>
    <div class="section">
        <p class="category">Local
            <br> <span style="font-size: 12px">( If you don't have this project up and running locally, the local links will not work ) <span> </p>
        <div class="sub-section">
            <div>
                <p class="service-name">Webpage</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="http://127.0.0.1:8000/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8000</span>
                    </div>
                </div>
                <div class="popup" id="webPageInfo">
                    <p><strong>Webpage:</strong></p>
                    <button onclick="togglePopup('webPageInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">MySQL</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 3306</span>
                        <button class="more-info-btn" onclick="togglePopup('MySQLInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="MySQLInfo">
                    <p><strong>MySQL:</strong></p>
                    <div class="info-popup">
                        DATABASE: <strong>jgomes_site_dev</strong> <br><br>

                        HOST: <strong> 127.0.0.1 </strong>
                        PORT: <strong>3306</strong> <br><br>

                        USERNAME: <strong>user_dev</strong>
                        PASSWORD: <strong>pass_dev</strong>
                    </div>
                    <button onclick="togglePopup('MySQLInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">PhpMyAdmin</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="http://127.0.0.1:8090/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8090</span>
                        <button class="more-info-btn" onclick="togglePopup('PhpMyAdminInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="PhpMyAdminInfo">
                    <p><strong>PhpMyAdmin:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('PhpMyAdminInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 5672</span>
                        <button class="more-info-btn" onclick="togglePopup('RabbitMQInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="RabbitMQInfo">
                    <p><strong>RabbitMQ:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('RabbitMQInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ Interface</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="http://127.0.0.1:15672/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 15672</span>
                        <button class="more-info-btn" onclick="togglePopup('RabbitMQInterfaceInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="RabbitMQInterfaceInfo">
                    <p><strong>RabbitMQ Interface:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('RabbitMQInterfaceInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ API</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="http://127.0.0.1:15672/api/queues/%2F/messages_dev">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 15672</span>
                        <button class="more-info-btn" onclick="togglePopup('RabbitMQApiInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="RabbitMQApiInfo">
                    <p><strong>RabbitMQ API:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('RabbitMQApiInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">Redis</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 6379</span>
                        <button class="more-info-btn" onclick="togglePopup('RedisInfo')">More</button>
                    </div>
                </div>
                <div class="popup" id="RabbitMQInfo">
                    <p><strong>RabbitMQ:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('RedisInfo')">Close</button>
                </div>
            </div>
            <div>
                <p class="service-name">Redis Commander</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="http://127.0.0.1:8081">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8081</span>
                        <button class="more-info-btn" onclick="togglePopup('RedisCommander')">More</button>
                    </div>
                </div>
                <div class="popup" id="RedisCommander">
                    <p><strong>RabbitMQ API:</strong></p>
                    <div class="info-popup">
                        USERNAME: <strong> user_dev </strong>
                        PASSWORD: <strong> pass_dev </strong>
                    </div>
                    <button onclick="togglePopup('RedisCommander')">Close</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="section">
        <p class="category">Production</p>
        <div class="sub-section">
            <div>
                <p class="service-name">Web Page</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jgomes.site">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 443</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">MySQL</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 3306</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">PhpMyAdmin</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jgomes.site/phpmyadmin/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8091</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 5672</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ Interface</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jgomes.site/rabbitmq/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 15672</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">RabbitMQ API</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jgomes.site/rabbitmq/api/queues/%2F/messages_prod">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 15672</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">Redis</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 6379</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">Redis Commander</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jgomes.site/redis/">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8081</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="section">
        <p class="category">CI/CD</p>
        <div class="sub-section">
            <div>
                <p class="service-name">Jenkins</p>
                <div>
                    <div>
                        <span>
                            ( Internal )
                        </span>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 50001</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="service-name">Jenkins Interface</p>
                <div>
                    <div>
                        <a class="link" target="_blank" href="https://jjenkins.xyz">Link</a>
                    </div>
                    <div style="float: right">
                        <span style="margin-right: 10px">Port: 8891</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popups = document.querySelectorAll('.popup');
        popups.forEach(popup => popup.style.display = 'none');
    });

    function togglePopup(popupId) {
        const popup = document.getElementById(popupId);
        if (popup) {
            const popups = document.querySelectorAll('.popup');
            popups.forEach(otherPopup => {
                if (otherPopup.id !== popupId) {
                    otherPopup.style.display = 'none';
                }
            });

            popup.style.display = (popup.style.display === 'none' || popup.style.display === '') ? 'block' : 'none';
        }
    }
</script>
</body>
</html>
