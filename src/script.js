document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("article-form");

    const WP_URL = "http://intranet2.saint-andre.re/wp-json/wp/v2";
    const WP_USER = "user"; 
    const WP_PASS = "user_psw"; 
    const AUTH = "Basic " + btoa(`${WP_USER}:${WP_PASS}`);

    // Fonction pour uploader un fichier vers WP Media
    async function uploadFile(file) {
        const formData = new FormData();
        formData.append("file", file);

        const res = await fetch(`${WP_URL}/media`, {
            method: "POST",
            headers: { "Authorization": AUTH },
            body: formData
        });

        if (!res.ok) throw new Error("Erreur upload média");
        const data = await res.json();
        return data.id; // Retourne l'ID du média WP
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
            const data = new FormData(form);

            // 1️⃣ Upload couverture (facultative)
            let coverID = null;
            const coverFile = data.get("cover-image");
            if (coverFile && coverFile.size > 0) {
                coverID = await uploadFile(coverFile);
            }

            // 2️⃣ Upload PDFs
            const pdfFiles = data.getAll("pdf-files");
            const pdfIDs = [];
            for (const pdf of pdfFiles) {
                if (pdf && pdf.size > 0) {
                    const id = await uploadFile(pdf);
                    pdfIDs.push(id);
                }
            }

            // 3️⃣ Récupérer les catégories
            const categories = Array.from(form.querySelectorAll("input[name='categories']:checked"))
                .map(cb => cb.value);

            // 4️⃣ Créer le brouillon
            const postData = {
                title: data.get("article-title"),
                content: data.get("description"),
                status: "draft",
                categories: categories,
                excerpt: data.get("description"),
                featured_media: coverID, // null si pas d'image
                meta: { keywords: data.get("keywords") }
            };

            const res = await fetch(`${WP_URL}/posts`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": AUTH
                },
                body: JSON.stringify(postData)
            });

            if (!res.ok) throw new Error("Erreur création brouillon");

            alert("Brouillon créé avec succès !");
            form.reset();

        } catch (err) {
            console.error(err);
            alert("Erreur lors de la création de l'article. Vérifiez la console.");
        }
    });
});
