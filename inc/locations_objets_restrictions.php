<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Retourne les déclarations des champs inséré via la pipeline "prix_objets_extensions"
 *
 * @param array $valeurs
 * @return array
 */
function locations_objets_restrictions_extensions_declaration($valeurs = array()) {

	return pipeline(
			'locations_objets_restrictions_extensions', array(
				'data' => array(),
				'args' => $valeurs,
			)
		);
}