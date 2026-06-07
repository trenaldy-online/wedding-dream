<style>
/* SHARED FORM EDIT HELPERS */

.form-section {
    padding-bottom: 30px;
    margin-bottom: 30px;
    border-bottom: 1px solid var(--soft-border);
}

.form-section:last-child {
    border-bottom: 0;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-section-heading {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
}

.form-section-icon {
    width: 38px;
    height: 38px;
    border-radius: 14px;
    background: var(--gold-soft);
    color: var(--gold-dark);
    display: grid;
    place-items: center;
    font-weight: 900;
}

.form-section-title {
    margin: 0;
    color: var(--navy);
    font-size: 18px;
    font-weight: 900;
}

.form-section-subtitle {
    color: var(--muted);
    font-size: 13px;
    margin-top: 3px;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.form-grid-1 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
}

.form-help {
    color: var(--muted);
    font-size: 13px;
    margin-top: 7px;
}

.invitation-submit-row {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 28px;
}

.btn-gold-inline {
    width: auto;
    min-width: 180px;
    border: 0;
    background: var(--gold);
    color: white;
    border-radius: 14px;
    height: 52px;
    padding: 0 24px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 12px 24px rgba(216, 181, 50, 0.28);

    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    line-height: 1;
    white-space: nowrap;
    font-family: inherit;
    font-size: 15px;
    box-sizing: border-box;
}

.btn-gold-inline:hover {
    background: var(--gold-dark);
    color: white;
    text-decoration: none;
}

.btn-gold-inline:focus,
.btn-gold-inline:focus-visible {
    outline: none;
    box-shadow: 0 0 0 4px rgba(207, 160, 79, 0.22), 0 12px 24px rgba(216, 181, 50, 0.28);
}

.btn-soft-inline {
    width: auto;
    min-width: 150px;
    text-decoration: none;
    border: 1px solid var(--border);
    background: #fffdf7;
    color: var(--navy);
    border-radius: 14px;
    height: 52px;
    padding: 0 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.btn-soft-inline:hover {
    background: var(--gold-soft);
    color: var(--navy);
}

@media (max-width: 700px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
    }

    .invitation-submit-row {
        flex-direction: column;
    }

    .btn-gold-inline,
    .btn-soft-inline {
        width: 100%;
    }
}
</style>