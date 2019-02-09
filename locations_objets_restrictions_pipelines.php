<?php
/**
 * Utilisations de pipelines par Locations d&#039;objets - restrictions
 *
 * @plugin     Locations d&#039;objets - restrictions
 * @copyright  2019
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Locations_objets_restrictions\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function locations_objets_restrictions_affiche_milieu($flux) {
	include_spip('inc/config');
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets_cibles = lire_config('locations_objets_restrictions/objets', array());

	// Objets_informations sur les objets choisis.
	if (!$e['edition'] and in_array($e['table_objet_sql'], $objets_cibles)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'restrictions',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux 
 *   Données du pipeline
 * @return array
 *   Données du pipeline
 */
function locations_objets_restrictions_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('restriction'=>'*'), '*');

	return $flux;
}

/**
 * Compléter le tableau d’erreurs renvoyé par la fonction verifier d'un formulaire CVT.
 * 
 * @pipeline formulaire_verifier
 * @param array $flux
 *  Données du pipeline
 * @return array 
 *   Données du pipeline
 */
function locations_objets_restrictions_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'editer_objets_location'){
		include_spip('inc/locations_objets_restrictions');
		$definitions_saisies = chercher_definitions_restrictions();
		$verifier = charger_fonction('verifier', 'inc');
		$objet = _request('location_objet');
		$id_objet = _request('id_location_objet');

		// On détermine les restrictions attachées à l'objet de location.
		$sql = sql_select(
			'type_restriction,valeurs_restriction',
			'spip_restrictions_liens,spip_restrictions ', 'objet Like' . sql_quote($objet) . ' AND id_objet=' . $id_objet,
			 '', 
			 'rang_lien ASC');

		// Pour chaque restriction on vérifie si les valeurs des champs à tester contiennent des erreurs.
		while ($row sql_fetch($sql)) {
			$type_restriction = $row['type_restriction'];
			$definitions_saisie = $definitions_saisies[$type_restriction];
			if (isset($definitions_saisie['verifier']) AND isset($definitions_saisie['verifier']['champs'])) {
				foreach ($definitions_saisie['verifier']['champs'] = $champ) {
					// S'il n'existe pas déjà d'erreur pour le champ en question, on verifie via la vérification correspondante
					// au type de restriction.
					if (!isset($flux['data'][$champ]) AND 
						$erreur = $verifier(
							$champ, 
							$type_restriction, 
							array(
								'valeurs_restriction'=> json_decode($row['valeurs_restriction'], TRUE)))) {
						$flux['data'][$champ] = $erreur;
					}
				}
			}

		}
		
	}

	return $flux;
}
