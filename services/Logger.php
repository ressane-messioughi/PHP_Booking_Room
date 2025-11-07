<?php

// Fichier: backend/src/Logger.php

class Logger
{
    // Définition des codes de couleur
    private const RESET = "\033[0m";
    private const GRAY = "\033[90m";
    private const RED = "\033[91m";
    private const GREEN = "\033[92m";
    private const YELLOW = "\033[93m";
    private const BLUE = "\033[94m";
    private const MAGENTA = "\033[95m";
    private const CYAN = "\033[96m";

    /**
     * Écrit un message dans la console (stderr) avec des couleurs.
     *
     * @param string $level (INFO, ERROR, WARN, ROUTING, DEBUG)
     * @param string $message Le message à logger
     */
    public static function log(string $level, string $message): void
    {
        $timestamp = date('d-M-Y H:i:s');
        $levelUpper = strtoupper($level);

        // Choisir la couleur en fonction du niveau
        $color = match ($levelUpper) {
            'ERROR' => self::RED,
            'WARN' => self::YELLOW,
            'INFO' => self::BLUE,
            'SUCCESS' => self::GREEN,
            'ROUTING' => self::MAGENTA,
            'DEBUG' => self::CYAN,
            default => self::GRAY,
        };

        // Formatage du message
        $logLine = sprintf(
            "%s[%s]%s %s[%-7s]%s %s\n",
            self::GRAY,    // Couleur de la date
            $timestamp,
            self::RESET,
            $color,        // Couleur du niveau
            $levelUpper,   // Niveau (ex: "INFO", "ERROR")
            self::RESET,
            $message       // Le message lui-même
        );

        // Écrire sur "stderr" pour que ça s'affiche dans la console
        // NE PAS FAIRE "echo", sinon ça part dans la réponse HTTP !
        file_put_contents('php://stderr', $logLine);
    }
}