[Retour](index.md)

## Information pour développeurs ##

### B.D.D. ###  
Une nouvelle table est nécessaire pour conserver les intervalles produit de la planification.  

#### tool_taskattestoodle

|  id     | trainingid  | executiondate | beginperiod | endperiod | mailto   | operatorid | auto  | togenerate |
|---------|-------------|---------------|-------------|-----------|----------|------------|-------|------------|
| int(10) | int(10)     | int(10)       | int(10)     | int(10)   | char(255)| int(10)    | int(1)| int(1)     |

Ensemble des formations Attestoodle.  

id = Identifiant technique d'un intervalle planifié  
trainingid = Identifiant de la formation concernée par cet intervalle  
executiondate = Date sous la forme de timestamp, date à laquelle sera généré les attestions sur l'intervalle  
beginperiod = Date de début l'intervalle  
endperiode = Date de fin de l'intervalle  
mailto : Adresse mail à notifier, ou vide si aucune notification n'est souhaitée  
auto : Booléen précisant s'il faut ou non générer les attestations lorsqu'on arrive à la date d'échéance.  
togenerate : Booléen précisant si le traitement va générer ou non les attestations, s'il vaut 0 les attestations ne seront pas générées autiomatiquement.  
