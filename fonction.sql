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

CREATE OR REPLACE FUNCTION get_user_task_by_date(p_id_user INT)
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

CREATE OR REPLACE FUNCTION get_tasks_by_user_and_state(p_id_user INT, p_etat_tache VARCHAR)
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

