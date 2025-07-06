<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projet approuvé</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 30px;">
    <div style="background-color: white; padding: 20px; border-radius: 6px; max-width: 600px; margin: auto;">
        <h2 style="color: #2c3e50;">Bonjour,</h2>

        <p>
            Le projet intitulé <strong style="color: #34495e;">{{ $projet->titre_projet }}</strong>
            auquel vous avez été ajouté comme collaborateur a été <strong style="color: green;">approuvé</strong>.
        </p>

        <p>
            Vous pouvez désormais le consulter ou effectuer les actions autorisées dans votre espace collaborateur.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/projects/' . $projet->id) }}"
               style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
                Voir le projet
            </a>
        </div>

        <p>Merci pour votre collaboration.</p>
        <p style="margin-top: 40px;">Cordialement,<br>L’équipe Projet</p>
    </div>
</body>
</html>
