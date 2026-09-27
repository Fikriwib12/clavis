/**
 * Client-side validation that mirrors the server-side Form Request rules.
 *
 * A field opts in with `data-rules` (for example "required|between:6,15|alpha_num") and names itself
 * in messages with `data-label`. Messages are written to the element whose `data-error-for` matches
 * the field id, the same element Blade uses to render server-side errors, and use the same wording
 * as app/Http/Requests/Concerns/HasIndonesianMessages.php.
 */

const patterns = {
    alphaNum: /^[A-Za-z0-9]+$/,
    alphaSpace: /^[\p{L} ]+$/u,
    teamName: /^[A-Za-z0-9_]+$/,
    digits: /^[0-9]+$/,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
};

/** Count characters the way PHP's mb_strlen() does, so multibyte letters count once. */
const characterCount = (value) => [...value].length;

/** Collapse whitespace the way Str::squish() does before names are compared on the server. */
const squish = (value) => value.trim().replace(/\s+/g, ' ');

const rules = {
    required: (value, params, { label, field }) =>
        value === '' ? `${label} wajib ${field.tagName === 'SELECT' ? 'dipilih' : 'diisi'}.` : null,

    between: (value, [min, max], { label }) => {
        const length = characterCount(value);

        return length < Number(min) || length > Number(max)
            ? `${label} harus terdiri dari ${min}-${max} karakter.`
            : null;
    },

    max: (value, [max], { label }) => (characterCount(value) > Number(max) ? `${label} maksimal ${max} karakter.` : null),

    alpha_num: (value, params, { label }) =>
        patterns.alphaNum.test(value) ? null : `${label} hanya boleh berisi huruf dan angka.`,

    alpha_space: (value, params, { label }) =>
        patterns.alphaSpace.test(value) ? null : `${label} hanya boleh berisi huruf dan spasi.`,

    team_name: (value, params, { label }) =>
        patterns.teamName.test(value) ? null : `${label} hanya boleh berisi huruf, angka, dan garis bawah (_).`,

    email: (value, params, { label }) =>
        patterns.email.test(value) ? null : `${label} harus berupa alamat email yang valid.`,

    digits_between: (value, [min, max], { label }) =>
        patterns.digits.test(value) && value.length >= Number(min) && value.length <= Number(max)
            ? null
            : `${label} harus berupa angka dengan panjang ${min}-${max} digit.`,

    in: (value, options, { label }) => (options.includes(value) ? null : `${label} yang dipilih tidak valid.`),

    different: (value, [otherId], { label }) => {
        const other = document.getElementById(otherId);

        if (!other || squish(other.value).toLowerCase() !== squish(value).toLowerCase()) {
            return null;
        }

        return `${label} tidak boleh sama dengan ${(other.dataset.label ?? other.name).toLowerCase()}.`;
    },

    same: (value, [otherId], { label }) => {
        const other = document.getElementById(otherId);

        return other && other.value !== value ? `${label} tidak cocok.` : null;
    },
};

const parseRules = (definition) =>
    definition
        .split('|')
        .filter(Boolean)
        .map((rule) => {
            const [name, params = ''] = rule.split(':');

            return { name, params: params === '' ? [] : params.split(',') };
        });

/** Passwords are not trimmed by the server (TrimStrings skips them), every other field is. */
const valueOf = (field) => (field.type === 'password' ? field.value : field.value.trim());

/**
 * @param {HTMLInputElement|HTMLSelectElement} field
 * @returns {string|null} The first failing rule's message, or null when the field is valid.
 */
export function validateField(field) {
    const value = valueOf(field);
    const context = { field, label: field.dataset.label ?? field.name };

    for (const { name, params } of parseRules(field.dataset.rules ?? '')) {
        if (value === '' && name !== 'required') {
            continue;
        }

        const message = rules[name]?.(value, params, context) ?? null;

        if (message !== null) {
            return message;
        }
    }

    return null;
}

function renderError(field, message) {
    const error = field.form?.querySelector(`[data-error-for="${field.id}"]`);

    if (message === null) {
        field.removeAttribute('aria-invalid');
    } else {
        field.setAttribute('aria-invalid', 'true');
    }

    if (error) {
        error.textContent = message ?? '';
        error.hidden = message === null;
    }
}

function setSubmitting(form, isSubmitting) {
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = isSubmitting;
    });
}

export function initFormValidation(root = document) {
    const forms = [...root.querySelectorAll('form[data-validate]')];

    forms.forEach((form) => {
        const fields = [...form.querySelectorAll('[data-rules]')];

        // The browser's own bubbles are replaced by the inline messages below.
        form.noValidate = true;

        fields.forEach((field) => {
            const validate = () => renderError(field, validateField(field));

            field.addEventListener('input', () => {
                field.dataset.dirty = 'true';

                if (field.hasAttribute('aria-invalid')) {
                    validate();
                }
            });

            field.addEventListener('change', validate);

            field.addEventListener('blur', () => {
                if (field.dataset.dirty === 'true') {
                    validate();
                }
            });
        });

        form.addEventListener('submit', (event) => {
            const invalidFields = fields.filter((field) => {
                const message = validateField(field);

                renderError(field, message);

                return message !== null;
            });

            if (invalidFields.length > 0) {
                event.preventDefault();
                invalidFields[0].focus();

                return;
            }

            // Prevent a double submission from registering the same data twice.
            setSubmitting(form, true);
        });
    });

    // A page restored from the back/forward cache keeps its disabled buttons, so enable them again.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            forms.forEach((form) => setSubmitting(form, false));
        }
    });
}
