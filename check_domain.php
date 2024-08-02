<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $domain = htmlspecialchars($_POST['domain']);
    $tld = htmlspecialchars($_POST['tld']);
    $fullDomain = $domain . $tld;


    require 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Tus credenciales de Namecheap desde el archivo .env
    $apiUser = $_ENV['API_USER'];
    $apiKey = $_ENV['API_KEY'];
    $userName = $_ENV['USER_NAME'];
    $clientIp = $_ENV['CLIENT_IP'];

    // URL de la API para el entorno de sandbox
    $url = "https://api.sandbox.namecheap.com/xml.response?ApiUser=$apiUser&ApiKey=$apiKey&UserName=$userName&Command=namecheap.domains.check&ClientIp=$clientIp&DomainList=$fullDomain";

    // Realizar la solicitud HTTP GET
    $response = file_get_contents($url);

    // Parsear la respuesta XML
    $data = new SimpleXMLElement($response);

    // Verificar el estado de la respuesta
    if ((string)$data->attributes()->Status === "OK") {
        foreach ($data->CommandResponse->DomainCheckResult as $domainResult) {
            $domainName = $domainResult['Domain'];
            $isAvailable = $domainResult['Available'] == 'true' ? "Sí" : "No";
            echo "<h2>Dominio: $domainName</h2>";
            echo "<p>Disponible: $isAvailable</p>";
        }
    } else {
        echo "Error en la llamada a la API: " . $data->Errors->Error;
    }
}
?>
