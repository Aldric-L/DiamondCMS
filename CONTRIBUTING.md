****************************************************|- LICENCE (CONTRIBUTIONS) -|****************************************************
Les contributions au projet DiamondCMS doivent être soumises à Aldric L. avant toute publication. 
Quelque soit la taille de celles-ci, le projet reste son entière propriété et la licence générale reste inchangée.

TOUTEFOIS, ce principe admet plusieurs exceptions :
- les addons (partie du code indépendante permettant d'ajouter des fonctionnalitées) sont l'entière propriété de leurs auteurs
- les thèmes (partie du code permettant la modification de l'apparence du CMS), en ce qu'ils sont la modification du code d'Aldric L., doivent êtres publiés sous licence libre, et aucun commerce ne saurait en être fait.
****************************************************|- LICENCE (CONTRIBUTIONS) -|****************************************************

En ce qui concerne les spécificités techniques de ces contributions :

- Pour les modifications du "noyau", c'est-à-dire des fichiers controlleurs et modèles, ils doivent respecter les conventions préétablies et être précédé d'au moins une ligne de commentaire, afin de garder un code le plus lisible possible. 

- Pour les créations d'addons, ceux-ci doivent avoir un dossier qui leur est propre (le nom doit être le nom de l'addon, les espaces ne sont pas permis). Ce dossier doit impérativement contenir un fichier init.php qui est inclu automatiquement lors du chargement de chaque page. Le rangement et la structure du reste des sources sont laissés à la discrétion du créateur, tant que les règles sus-énoncées sont respectées. On adoptera aussi comme convention que seuls les fichiers situés à la racine de l'addon ont la permission d'intéragir avec les autres fichiers sources du CMS.
Les addons peuvent aussi rajouter des pages au CMS : ils sont automatiquement appelé lorsqu'une URL est sous la forme : http://example.com/NomExactDuDossierDeLAddon/page ou http://example.com/admin/NomExactDuDossierDeLAddon/page . Dans le premier cas, le CMS cherchera dans le dossier controller à la racine de l'addon un fichier page.php, dans le second, il cherchera le même fichier dans le sous-dossier admin du dossier controller de l'addon (ex : NomExactDuDossierDeLAddon/controllers/admin/page.php). Il est vivement suggéré que les addons respectent l'architecture MVC.
Attention: Les addons ne sont pas limités en terme de permissions : un addon peut modifier absolument tous les fichiers du CMS. Il convient donc pour les utilisateurs d'être très précautionneux lors de l'installation de ceux-ci. 

- Pour les thèmes, la seule contrainte est d'inclure un fichier (ini) à la racine du thème indiquant la version du thème, la version du CMS supportée, et le nom du thème. Une licence peut être inclue (à condition évidemment qu'elle respecte la licence sus-énoncée).

