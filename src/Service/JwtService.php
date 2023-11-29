<?php

namespace App\Service;

use Monolog\DateTimeImmutable;


class JwtService
{
    public function generate(array $header,array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0)
        {
            $now = new DateTimeImmutable("now");
            $exp = $now->getTimeStamp() + $validity;

            $payload['iat'] = $now->getTimeStamp();
            $payload['exp'] = $exp;
        }

        

        // On encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // On nettoie les valeurs encodées (retrait des +, / et =)
        $base64Header = str_replace(['+','/','='],['-','_',''], $base64Header);
        $base64Payload = str_replace(['+','/','='],['-','_',''],$base64Payload);

        // Générer la signature
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $base64Header.'.'.$base64Payload,$secret,true);
        $base64signature = base64_encode($signature);
        $base64signature = str_replace(['+','/','='],['-','_',''],$base64signature);

        // On crée le token
        $jwt = $base64Header.'.'.$base64Payload.'.'.$base64signature;
        return $jwt;
    }

    // Token is_valide ?
    public function isValid($token): ?bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    // On récupère le payload
    public function getPayload(string $token): array
    {
        // On démonte le token
        $array = explode('.',$token);

        // On décode le payload
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }
    // On récupère le header
    public function getHeader(string $token): array
    {
        // On démonte le token
        $array = explode('.',$token);

        // On décode le header
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }
    // Token encore valide ?
    public function isExpire($token): ?bool 
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable('now');

        return $payload['exp'] < $now->getTimeStamp();
    }
    // On vérifie la signature du token
    public function check(string $token, string $secret): ?bool
    {
        // ON récupère le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Regénération du token
        $verifToken = $this->generate($header,$payload,$secret,0);
        return $token === $verifToken;
    }

}
