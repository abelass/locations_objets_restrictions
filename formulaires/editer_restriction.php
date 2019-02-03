<?php
/**
 * Gestion du formulaire de d'édition de restriction
 *
 * @plugin     Locations d&#039;objets - restrictions
 * @copyright  2019
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Locations_objets_restrictions\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_restriction_identifier_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_restriction), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de restriction
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_restriction_charger_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	include_spip('inc/prix_objets');
	$valeurs = formulaires_editer_objet_charger('restriction', $id_restriction, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
    // Inclure les extensions.
    $valeurs['_saisies_extras'] = prix_objets_extensions_declaration($valeurs);
    $extensions = array_keys($valeurs['_saisies_extras']);
    $saisies = array();

    foreach ($valeurs['_saisies_extras'] as $s) {
      $saisies = array_merge($saisies, $s);
      foreach (saisies_lister_par_nom($s) as $nom => $definition) {
        $valeurs[$nom] = _request($nom);
      }
    }

    // Déclarer les extensions
    if (is_array($extensions) and count($extensions) > 0) {

      $valeurs['extensions'] = _request('extensions');

      $valeurs['_saisies_extras'] = array_merge(
        array(
          array(
            'saisie' => 'hidden',
            'options' => array(
              'nom' => 'extensions',
              'defaut' => implode(',', $extensions),
            )
          ),
          array(
            'saisie' => 'fieldset',
            'options' => array(
              'nom' => 'extensions',
              'label' => _T('prix_objets:info_extensions'),
            ),
            'saisies' =>  $saisies,
          )
        )
        );
    }	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de restriction
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_restriction_verifier_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('restriction', $id_restriction, array('titre', 'extension', 'id_extension'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de restriction
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_restriction_traiter_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('restriction', $id_restriction, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_restriction = $retours['id_restriction']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			
			objet_associer(array('restriction' => $id_restriction), array($objet => $id_objet));
			
			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_restriction, '&');
			}
		}
	}

	return $retours;
}
