<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nouveau Projet Supervision</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;">
    <div style="max-width: 600px; background-color: #fff; padding: 20px; margin: auto; border-radius: 10px;">
        <h2 style="color: #2c3e50;">Bonjour {{ $superviseur->nom_sup }},</h2>
        <p style="font-size: 16px;">
            Vous avez été désigné comme <strong>superviseur</strong> pour le projet suivant :
        </p>
        <p style="font-size: 18px; font-weight: bold;">{{ $projectName }}</p>
        <p>
            Cliquez sur le bouton ci-dessous pour voir les détails du projet :
        </p>
        <p style="text-align: center;">
            <a href="{{ url('/projets/' . $projectId) }}" style="background-color: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Voir le projet
            </a>
        </p>
        <hr>
        <p style="font-size: 14px; color: #7f8c8d;">Merci pour votre collaboration,<br>L’équipe de gestion de projets</p>
    </div>
</body>
</html>
