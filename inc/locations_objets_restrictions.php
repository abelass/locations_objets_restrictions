<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Retourne les champs spécifique d'un type de restriction.
 *
 * @param string $type_restriction
 *        	Le type de restriction.
 * @param array $valeurs
 *        	Des valeurs par défaut.
 * @param
 *        	array options:
 *        	- champs_specifiques: si oui filtre le tableau pour obtenir uniquement les champs
 *        	spécifiques.
 *
 * @return array Les champs de la restriction.
 */
function lor_definition_saisies($type_restriction, $valeurs = [], $options = []) {

	$restrictions = chercher_definitions_restrictions($valeurs);

	if (is_array($restrictions)) {
		// Chercher les fichiers restrictions
		$type_restrictions_noms = [];
		$restrictions_saisies = [];
		foreach ($restrictions as $nom => $definition) {
			if (isset($definition['saisies'])) {
				$restrictions_saisies[$nom] = [
					[
						'saisie' => 'fieldset',
						'options' => [
							'nom' => 'specifique',
							'label' => _T('restriction:label_parametres_specifiques'),
						],
						'saisies' => $definition['saisies']
					]
				];
			}
		}
	}

	// Obtenir les champs spécifiques
	if ($type_restriction and isset($restrictions_saisies[$type_restriction])) {
		$saisies = $restrictions_saisies[$type_restriction];
	}

	return $saisies;
}

/**
 * Cherche les définitions des restrictions disponibles.
 *
 * @param array $valeurs
 *
 * @return array]
 */
function chercher_definitions_restrictions($valeurs = []) {
	$definitions_restrictions = find_all_in_path("restrictions/", '^');
	$restrictions = [];
	if (is_array($definitions_restrictions)) {
		foreach ($definitions_restrictions as $fichier => $chemin) {
			list ($nom, $extension) = explode('.', $fichier);
			// Charger la définition des champs

			if ($defs = charger_fonction($nom, "restrictions", true)) {
				if (is_string($valeurs)) {
					$valeurs = unserialize($valeurs);
				}
				$restriction = $defs($valeurs);
				$restrictions[$nom] = $restriction;
			}
		}
	}
	return $restrictions;
}
