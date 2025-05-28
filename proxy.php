<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    $apiKey = "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";

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "gpt-3.5-turbo",
        "messages" => $input["messages"]
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data["choices"][0]["message"]["content"])) {
        echo json_encode($data);
    } else {
        // Fallback bei leerer oder fehlerhafter Antwort
        $lastUserMsg = end($input["messages"])["content"];
        if (stripos($lastUserMsg, "wetter") !== false) {
            echo json_encode([
                "choices" => [[
                    "message" => [
                        "content" => "🌦️ Ich kann leider keine Live-Wetterdaten abrufen. Du kannst aber z. B. auf wetter.com nachsehen. ☁️"
                    ]
                ]]
            ]);
        } else {
            echo json_encode([
                "choices" => [[
                    "message" => [
                        "content" => "⚠️ Entschuldigung, ich konnte deine Frage gerade nicht beantworten. Versuch es bitte erneut."
                    ]
                ]]
            ]);
        }
    }
    exit;
}
http_response_code(405);
echo json_encode(["error" => "Only POST allowed"]);
