#!/usr/bin/env php
<?php
/**
 * WebSocket Server Startup Script
 * 
 * This script starts the WebSocket server for real-time messaging.
 * Run this in a separate terminal: php websocket-server.php
 * 
 * The server will listen on ws://localhost:8080
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/WebSocket/ChatServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\ChatServer;

echo "==============================================\n";
echo "  SeedCycle WebSocket Server\n";
echo "==============================================\n";
echo "Starting WebSocket server on port 8080...\n";
echo "Press Ctrl+C to stop the server.\n";
echo "==============================================\n\n";

try {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new ChatServer()
            )
        ),
        8080
    );

    echo "✓ WebSocket server is running!\n";
    echo "  Listening on: ws://localhost:8080\n\n";
    
    $server->run();
} catch (Exception $e) {
    echo "✗ Failed to start WebSocket server:\n";
    echo "  " . $e->getMessage() . "\n\n";
    echo "Common issues:\n";
    echo "  - Port 8080 is already in use\n";
    echo "  - Composer dependencies not installed (run: composer install)\n";
    echo "  - Database connection failed\n\n";
    exit(1);
}
