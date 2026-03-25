
document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash.startsWith("#post")) {
        const target = document.querySelector(hash);
        if (target) {
            target.classList.add("highlight-post");

            setTimeout(() => {
                target.classList.remove("highlight-post");
            }, 4000);
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('textarea[data-editor="nx_editor"]').forEach(function (textarea) {
        const form = textarea.form;
        if (!form) {
            return;
        }

        const wrapper = textarea.parentElement?.querySelector(".nx-editor");
        const editor = wrapper?.querySelector(".nx-editor-content");
        const source = wrapper?.querySelector(".nx-editor-source");

        const syncTextarea = function () {
            if (source && source.style.display !== "none") {
                textarea.value = source.value;
                return;
            }

            if (editor) {
                textarea.value = editor.innerHTML;
            }
        };

        editor?.addEventListener("input", syncTextarea);
        source?.addEventListener("input", syncTextarea);

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (submitter) {
            submitter.addEventListener("click", syncTextarea);
        });

        syncTextarea();
    });
});

const NX_CSRF = window.NX_FORUM_CSRF || "";

document.addEventListener("click", async function (e) {
    const btn = e.target.closest(".forum-like-btn");
    if (!btn) return;

    if (btn.dataset.busy === "1") return;
    btn.dataset.busy = "1";

    const postID = btn.dataset.post;
    const liked = btn.dataset.liked === "1";
    const icon = btn.querySelector("i");
    const countEl = btn.querySelector(".like-count");
    const globalCountEl = document.querySelector(`.like-count[data-like-count="${postID}"]`);

    const oldCount = parseInt(countEl.textContent, 10);
    const newLiked = !liked;
    const newCount = Math.max(0, oldCount + (newLiked ? 1 : -1));

    btn.dataset.liked = newLiked ? "1" : "0";
    countEl.textContent = newCount;
    if (globalCountEl) {
        globalCountEl.textContent = newCount;
    }

    btn.classList.toggle("btn-danger", newLiked);
    btn.classList.toggle("btn-outline-danger", !newLiked);
    btn.classList.toggle("liked", newLiked);

    icon.classList.toggle("bi-hand-thumbs-up-fill", newLiked);
    icon.classList.toggle("bi-hand-thumbs-up", !newLiked);

    try {
        const res = await fetch("/includes/plugins/forum/system/ForumLikePostAjax.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `postID=${postID}&action=${newLiked ? "like" : "unlike"}&csrf=${NX_CSRF}`
        });

        const data = JSON.parse(await res.text());

        if (data.deleted === true) {
            console.info("Post existiert nicht mehr - Like ignoriert");
            return;
        }

        if (data.success === true && typeof data.likes !== "undefined") {
            countEl.textContent = data.likes;
            if (globalCountEl) {
                globalCountEl.textContent = data.likes;
            }

            btn.dataset.liked = data.liked ? "1" : "0";
            return;
        }

        if (data.success === false && data.error) {
            if (data.error === "Own post") {
                alert("Du kannst deinen eigenen Beitrag nicht liken.");
            } else {
                console.warn("Like error:", data.error);
            }
        }
    } catch (err) {
        console.error("Like failed:", err);

        btn.dataset.liked = liked ? "1" : "0";
        countEl.textContent = oldCount;
        if (globalCountEl) {
            globalCountEl.textContent = oldCount;
        }

        btn.classList.toggle("btn-danger", liked);
        btn.classList.toggle("btn-outline-danger", !liked);
        btn.classList.toggle("liked", liked);

        icon.classList.toggle("bi-hand-thumbs-up-fill", liked);
        icon.classList.toggle("bi-hand-thumbs-up", !liked);
    }

    btn.dataset.busy = "0";
});

document.addEventListener("DOMContentLoaded", function () {
    const deleteModal = document.getElementById("confirmDeleteModal");
    if (!deleteModal) {
        return;
    }

    deleteModal.addEventListener("show.bs.modal", function (event) {
        const trigger = event.relatedTarget;
        const deleteUrl = trigger.getAttribute("data-delete-url");
        const confirmBtn = deleteModal.querySelector("#confirmDeleteBtn");
        confirmBtn.setAttribute("href", deleteUrl);
    });
});
