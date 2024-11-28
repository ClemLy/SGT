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

-- Fonction pour récupérer les tâches à échéance dans moins de 48 heures
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
        t.id_user = p_id_user
        AND t.etat_tache != 'Terminée'
        AND EXTRACT(EPOCH FROM (t.echeance_tache::TIMESTAMP - CURRENT_DATE::TIMESTAMP)) <= 86400*2  -- 48 heures
        AND EXTRACT(EPOCH FROM (t.echeance_tache::TIMESTAMP - CURRENT_DATE::TIMESTAMP)) > 0;  -- Pas encore échue
END;
$$ LANGUAGE plpgsql;