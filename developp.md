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

mailto = Adresse mail à notifier, ou vide si aucune notification n'est souhaitée  

auto = Booléen précisant s'il faut ou non générer les attestations lorsqu'on arrive à la date d'échéance.  

togenerate = Booléen précisant si le traitement va générer ou non les attestations, s'il vaut 0 les attestations ne seront pas générées autiomatiquement.  

### Graphe d'appel
Plan.php
	= classes/training_update_form

	=> listinterval.php
		=training.mustache
		=>interval.php
			=classes/training_interval_form
			<=listinterval.php

		<=plan.php
		<= attestoodle/index.php

 	<= attestoodle/index.php

Le source plan.php constitue le point d'entrée, selon l'existence ou non d'une planification, soit on reste sur ce source soit on est redirigé vers listinterval.php  

### Contrat avec Attestoodle (méthodes du source lib.php)
tool_taskattestoodle_deletetraining($trainingid)   
 * prise en compte de la suppression d'une formation  
Lors de la suppression d'une formation le Plugin est sollicité pour supprimer l'éventuelle planification de cette formation.
*A noter que cette méthode peut être placée dans d'autre plugin ayant besoin de nettoyer les informations liées à une formation.*

tool_taskattestoodle_get_interval($trainingid)  
 * Si la formation est planifiée, fournit l'intervalle le plus proche de la date courante, sinon fournit un intervalle à 0. C'est alors la classe plugins_accessor qui calculera un intervalle.  
Le retour prend la forme d'une structure sdtClass ayant les attributs d_start et d_end contenant les bornes de l'intervalle sous la forme de timestamp.  

task_link($trainingid)
 * Fournit un objet de type moodle_url vers la page de gestion de la planification (plan.php).
 
