<?php
if (empty($_SESSION['id'])) {
    header('Location: ./login');
    exit();
}
