[Retour](index.md)

## Modification des intervalles  ##  
Si la formation est déjà planifiée vous pouvez modifié chaque intervalle via l'écran suivant :  
![planif2](https://user-images.githubusercontent.com/26385729/69252801-dcd1a600-0bb3-11ea-9037-538885c21bb0.png)

Les modifications des intervalles sont entièrement manuelles, aucun calcul ne sera relancé !  
Seule l'application des règles d'intégrités suivantes est imposée :  
 * la date de fin doit être supérieure ou égale à la date du début
 * la date d'échéance doit être supérieur à la date du début de l'intervalle.  

**Les incohérences d'intervalles se chevauchant ou laissant une période non couverte sont laissées à la responsabilité de l'opérateur réalisant la saisie.**

Les actions possibles sont :
 * Le bouton "Réinitialiser" : Supprimer l'ensemble des intervalles et reviens à l'écran de planification.  
 * L'icône poubelle : Supprimer un intervalle  

L'icône ciseau : Scinder un intervalle si sa durée le permet (un intervalle correspond au minimum à une journée)  
L’icône crayon : Éditer un intervalle, permet de modifier l'ensemble des informations de l'intervalle via l'écran ci-dessous  

**Remarque** : la date d'échéance fait son affichage en précisant l'heure et le jour de la semaine (en anglais). La génération automatique sera lancée à partir de cette date, mais pas à cette date précise car le traitement est dépendant des tâches Moodle cf [Quand sera lancée la génération automatique ?](quand.md)

## Modifier un intervalle ##

![modifiuniq](https://user-images.githubusercontent.com/26385729/69415670-5634da00-0d15-11ea-974b-0b72dcc6c9a1.png)  

Vous pouvez changer les dates en respectant les règles de cohérence (date de début < date de fin et échéance > date de début)  
*Génération automatique* : coché si vous voulez ou non que la génération des attestations soit réalisée.  
*Fait* : coché si vous souhaitez que cet intervalle soit pris en compte ou non par les traitements automatiques.  
En décochant Fait sur un intervalle passé, vous demandez de générer à nouveau les attestations pour  cet intervalle.  
*E mail à notifier* : adresse mail qui recevra le compte rendu ou le rappel de la tâche. Si vous laissez vide ce champ aucun courriel ne sera lancé.  

## Détail du traitement automatique ##  
On récupère l'ensemble des tâches qui ont une date d'échéance dépassée sans être marqué 'Fait'.  
Pour chaque tâche :
 * si la génération automatique est demandée on génère les attestations de la formation.
 * si l'adresse mail est renseignée on envoie un courriel
 * on détermine la prochaine date d'échéance.
 * on marque la tâche comme Faite  
 
 
