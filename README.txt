######## SYSTEME DE GESTION DE PROJET COLLABORATIF - PHP & MySQL ##########

@- Description : 
        Plateforme collaboiratif de gestion de projet en équipe

@- Fonctionnalités :
    **Create project --> formulaire de création de projet et liste des projets existant
            - remplissez le formulaire pour créer un projet
            - lors de la création, choisissez les membres en précisant leur email suivie d'une virgule (,) à la fin de chaque mail
                exemple : email_1@email.com,email_2@email.com,
                NB : seuls les emails de membres qui ont un compte sera ajouté au projet (validé)
                        avant donc d'associer un email, assurez vous que le membre ai un compte
        
    **Manage task --> formulaire de création de tâches associé à un projet spécifique
                        - Création de tâches -
            - choisissez le projet pour lequel vous voulez ajouter des tâches (liste déroulante)
            - la liste des membres (emails) associés à se projet sera automatiquement disponiblie (liste déroulant : choisir membres)
            -choisir le membre (email) à qui ont souhait attribuer la tâche
            -renseigner la tâche et valider

                        - Réalisation de tâches -
            - dans la liste de droite, uniquement les tâches qui vous sont attribuées apparaissent
            - cliquez sur "Do Task" pour réaliser la tâche
            NB : la progression (réalisation des tâche) se fait selon une échelle de 0 à 100% (de 10% en 10%)
            - une fois la tâche réalisée à 100%, le boutton "Do Task" disparait
    
    **Dashboard --> tableau de bord, permettant d'avoir un apperçu global sur les projets
            - le boutton (cercle) (+) permet de voir la liste et le détails des tâches assoccié à un projet
                NB : appuyer à nouveau sur le bouton (+) pour faire disparaitre la boite de dialogue

    
    **Message --> discuter avec les membres sur la Plateforme
            - à gauche la liste des membres inscrits sur la Plateforme, cliquer sur le bouton (msg) pour converser



Compte par défaut d'utilisateur (pour effectuer des tests)
    /   email                / ---------> /mots de passe/
    	admin@admin.net                     azerty123
        users@email.com                     azerty123
        azerty@azerty.com                   azerty123


la base de donnees en complément (spl)

NB : utiliser un écran de PC

Bon usage
@by_mckwunthy