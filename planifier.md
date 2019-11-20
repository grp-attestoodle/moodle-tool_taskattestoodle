[Retour](index.md)

# Planifier une formation  #  
Si la formation n'est pas déjà planifiée vous devez remplir le formulaire suivant :


![planif1](https://user-images.githubusercontent.com/26385729/69242072-5eb7d400-0ba0-11ea-9dab-819bfd4e5650.png)

Vous pouvez redéfinir ici les dates de début et de fin de formation en respectant la cohérence entre les dates de début et de fin.  

## Détail des champs ##
**Génération automatique** : coché par défaut, le traitement automatique générera les attestations, si décoché le traitement automatique vous préviendra par courriel des formations qui doivent être attestées lorsque l'échéance sera échue.  

**Émail à notifier** : renseigné par défaut avec l'adresse mail de l'opérateur, vous pouvez changer l'adresse ou la mettre vide si vous ne désirez pas de messages prévenant de la génération des attestations faîtes ou à faire.  

**Nombre de génération** : indique en combien la période couverte par la formation doit être divisée, aussi ce nombre ne peut pas être égal à zéro et doit être inférieur ou égal à la durée en jour de la formation.  

**Heure de génération** : indique à partir de quelle heure les générations auront lieu [00:00, 23:59].  

**Décalage en jour avec la fin de période** : indiquez ici le nombre de jours qui sépare les dates de fin de l'intervalle avec la date de génération, ce nombre ne doit pas être supérieur à la durée d'un intervalle.  
Ce nombre peut être négatif si vous souhaitez que les attestations soient générées avant la fin de l'intervalle.  
On ne pourra pas rajouter des heures sur un intervalle déjà passé, le temps crédité résulte de la somme de temps jalons à leur date d’achèvement. Aussi avec une génération d'attestations en avance il est possible d'alerter les étudiants qui n'ont pas atteint le volume horaire nécessaire.  


## Algorithme de calcul ##
Le calcul des intervalles consiste en une répartition mathématique du nombre d'intervalle sur le temps de la formation.  
Lors du calcul les périodes passées sont marquées comme étant déjà générées, vous pourrez changer cela par la suite.  

Vous pourrez aussi modifier les dates des intervalles pour tenir compte d'éventuelles interruptions de formation.

[Consulter le résultat du calcul](modifier.md)
