-- Création de la fonction pour récupérer les tâches d'un utilisateur
CREATE OR REPLACE FUNCTION get_user_task(p_id_user INT)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE,
	id_categorie INT
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache,
		t.id_categorie
	FROM 
		TACHE t
	WHERE 
		t.id_user = p_id_user;
END;
$$ LANGUAGE plpgsql;

-- Création de la fonction pour récupérer les tâches par catégorie pour un utilisateur
CREATE OR REPLACE FUNCTION get_user_task_cate(p_id_categorie INT, p_id_user INT)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache
	FROM 
		TACHE t
	WHERE 
		t.id_categorie = p_id_categorie
		AND t.id_user = p_id_user;
END;
$$ LANGUAGE plpgsql;

-- Création de la fonction pour récupérer les tâches par ordre d'échéance pour un utilisateur
CREATE OR REPLACE FUNCTION get_user_task_by_date_ASC(p_id_user INT)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache
	FROM 
		TACHE t
	WHERE 
		t.id_user = p_id_user
	ORDER BY 
		t.echeance_tache ASC;  -- Tri par date d'échéance (ASC = du plus proche au plus lointain)
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION get_user_task_by_date_DESC(p_id_user INT)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache
	FROM 
		TACHE t
	WHERE 
		t.id_user = p_id_user
	ORDER BY 
		t.echeance_tache DESC;  -- Tri par date d'échéance (DESC = du plus lointain au plus proche)
END;
$$ LANGUAGE plpgsql;


-- Ceéation de la fonction pour récupérer les tâches selon leurs statut pour un utilisateur
CREATE OR REPLACE FUNCTION get_user_tasks_by_state(p_id_user INT, p_etat_tache VARCHAR)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache
	FROM 
		TACHE t
	WHERE 
		t.id_user = p_id_user
		AND t.etat_tache = p_etat_tache;
END;
$$ LANGUAGE plpgsql;

-- Création de la fonction pour récupérer les commentaires d'une tâche classés par date (du plus récent au plus ancien)
CREATE OR REPLACE FUNCTION get_task_comments(p_id_tache INT)
RETURNS TABLE (
	id_commentaire INT,
	text_commentaire TEXT,
	date_commentaire TIMESTAMP
) AS $$
BEGIN
	RETURN QUERY
	SELECT 
		c.id_commentaire,
		c.text_commentaire,
		c.date_commentaire
	FROM 
		COMMENTAIRE c
	WHERE 
		c.id_tache = p_id_tache
	ORDER BY 
		c.date_commentaire DESC;  -- Tri par date de commentaire (du plus récent au plus ancien)
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION get_tasks_due_in_48_hours(p_id_user INT)
RETURNS TABLE (
	id_tache INT,
	titre VARCHAR(255),
	description_tache TEXT,
	etat_tache VARCHAR(50),
	echeance_tache DATE,
	id_categorie INT
) AS $$
BEGIN
	-- Retourne les tâches concernées
	RETURN QUERY
	SELECT 
		t.id_tache,
		t.titre,
		t.description_tache,
		t.etat_tache,
		t.echeance_tache,
		t.id_categorie
	FROM 
		TACHE AS t
	WHERE 
		t.id_user = p_id_user
		AND t.etat_tache != 'Terminée'
		AND t.send_retard = FALSE
		AND EXTRACT(EPOCH FROM (t.echeance_tache::TIMESTAMP - CURRENT_TIMESTAMP)) <= 86400 * 2  -- 48 heures
		AND EXTRACT(EPOCH FROM (t.echeance_tache::TIMESTAMP - CURRENT_TIMESTAMP)) > 0;  -- Pas encore échue

    -- Met à jour send_retard pour les tâches concernées
    UPDATE TACHE AS tr
    SET send_retard = TRUE
    WHERE tr.id_user = p_id_user
      AND tr.etat_tache != 'Terminée'
      AND tr.send_retard = FALSE
      AND EXTRACT(EPOCH FROM (tr.echeance_tache::TIMESTAMP - CURRENT_TIMESTAMP)) <= 86400 * 2
      AND EXTRACT(EPOCH FROM (tr.echeance_tache::TIMESTAMP - CURRENT_TIMESTAMP)) > 0;
END;
$$ LANGUAGE plpgsql;


-- Faire un trigger qui, si une tache est supprimer vérifie si sa catégorie est encore lié à une autre tache, dans le cas contraire la supprime
CREATE OR REPLACE FUNCTION delete_category_if_no_task()
RETURNS TRIGGER AS $$
BEGIN
	IF NOT EXISTS (SELECT 1 FROM TACHE WHERE id_categorie = OLD.id_categorie) THEN
		DELETE FROM CATEGORIE WHERE id_categorie = OLD.id_categorie;
	END IF;
	RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_delete_category
AFTER DELETE ON TACHE
FOR EACH ROW
EXECUTE FUNCTION delete_category_if_no_task();

CREATE TRIGGER trigger_update_category
AFTER UPDATE ON TACHE
FOR EACH ROW
EXECUTE FUNCTION delete_category_if_no_task();