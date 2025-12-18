<?php
session_start();
require_once "../Connectdb/connect.php";

if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

// recupere id_personne et id_sportif
$id_personne = $_SESSION['id_personne'];
$stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
$stmtSportif->bind_param("i", $id_personne);
$stmtSportif->execute();
$resultSportif = $stmtSportif->get_result();
if ($resultSportif->num_rows === 0) die("Sportif introuvable");
$id_sportif = $resultSportif->fetch_assoc()['id_sportif'];

// recuperer id_reservation
$id_reservation = $_GET['id'] ?? null;
if (!$id_reservation){
  die("ID réservation manquant");
} 

// verifier que la reservation appartient au sportif
$stmtCheck = $conn->prepare("SELECT statut FROM reservation WHERE id_reservation = ? AND id_sportif = ?");
$stmtCheck->bind_param("ii", $id_reservation, $id_sportif);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$reservation = $resultCheck->fetch_assoc();
if (!$reservation){
    die("Réservation introuvable");
} 

/* mise à jour pour annuler */
$stmtCancel = $conn->prepare("UPDATE reservation SET statut = 'annule' WHERE id_reservation = ?");
$stmtCancel->bind_param("i", $id_reservation);
$stmtCancel->execute();

header("Location: reservations.php?msg=annule");
exit;
?>
