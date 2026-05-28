<?php
require_once '../app/Controllers/UserOrderController.php';
require_once '../app/Models/Shipment.php';
(new UserOrderController())->index();
