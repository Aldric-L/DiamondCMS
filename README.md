![DiamondCMS](https://aldric-l.github.io/DiamondCMS/img/logo.png)
# DiamondCMS


Bienvenue sur le dépot officiel de DiamondCMS.
Ici vous trouverez bien-sûr les sources et une documentation. 

**Le projet dispose aussi d'un site vitrine où vous retrouverez plus d'informations : https://aldric-l.github.io/DiamondCMS/**

## Téléchargement


**Attention : Ne téléchargez pas tout le repository !**

Téléchargez seulement ce dossier compressé : https://aldric-l.github.io/DiamondCMS/files/diamondcms-last.zip


## Documentation

Une documentation est disponible pour vous aider lors de l'installation et de l'utilisation de DiamondCMS.
**Attention : DiamondCMS nécessite PHP7.4 au moins pour fonctionner.**
Documentation : https://github.com/Aldric-L/DiamondCMS/wiki


*N'hésitez pas à nous contacter par les issues github si besoin ! Une template est disponible pour signaler un bug et pour demander de l'aide.*

Version actuelle : **1.1 Béta - Build G**

Lien pour accèder aux anciennes versions et aux dossiers de mise à jour (et ainsi accèder aux mises à jour en développement) : https://github.com/Aldric-L/DiamondCMS/tree/master/docs/files


**Changelog**
*1.1 Build G :*
- Patch de DiamondServerLink qui était défaillant depuis les MAJ de Steam.
- Ajout de la fonctionnalité d'envoyer des commandes RCON vers les serveurs depuis le panel admin.
- Ajout d'un log Rcon

*1.1 Build F :*
- Ajout de la fonctionnalité boutique externe et facilitation de la mise en place de contenus hors CMS

*1.1 Build E :*
- Patch critique PayPal
- Patch critique TinyMCE
- Ajout d'un phpinfo pour faciliter le dépannage

*1.1 Build D :*
- Patch des issues
- Support de PHP8

*1.1 Build C :*
- Patch de nombreux bugs non-critiques comme des erreurs d'affichage, et des problèmes sur le support et le forum.
- Code refactoring du CSS pour le rendre enfin lisible
- Sécurisation du CMS

*1.1 Build B (et non E comme c'est écrit dans un commit !) :*
- Dépreciation du réglage protocol
- Déploiement des erreurs inline

*1.1 Build A :*
- Réfection du forum et de la boutique
- Correction de failles de sécurité notamment avec les sessions
- Création d'un tracker d'erreurs
- Création d'un système de pages
- Animation de la page d'accueil en javascript
- Adaptation au DiamondCore 3.0
- Création d'un broadcast asynchrone en accueil admin
- Mise en oeuvre de mesures écologiques en réduisant les appels de mise à jour


*1.0 Build E :*
* Correction des problèmes de l'installateur (notamment pour l'URL rewriting et pour l'installation de la BDD)
* Ajout d'une fonction de broadcast
* Protection des roles Membre et diamond_master
* Patch d'un problème d'affichage admin/forum
