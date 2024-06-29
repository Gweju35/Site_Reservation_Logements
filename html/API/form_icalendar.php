<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Formulaire AJAX</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="icalendar.js"></script>
</head>
<body>

<form id="calendarForm">
    <label for="calendarUID">UID du calendrier :</label>
    <input type="text" id="calendarUID" name="calendarUID"><br><br>
    <label for="startDate">Date de début :</label>
    <input type="text" id="startDate" name="startDate" placeholder="YYYY-MM-DD HH:MM:SS"><br><br>
    <label for="endDate">Date de fin :</label>
    <input type="text" id="endDate" name="endDate" placeholder="YYYY-MM-DD HH:MM:SS"><br><br>
    <label for="summary">Titre :</label>
    <input type="text" id="summary" name="summary"><br><br>
    <label for="description">Description :</label>
    <input type="text" id="description" name="description"><br><br>
    <label for="location">Lieu :</label>
    <input type="text" id="location" name="location"><br><br>
    <button type="submit">Générer URL d'abonnement</button>
</form>

<div id="calendarURL"></div>


</body>
</html>
