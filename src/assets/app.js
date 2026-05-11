function wirePasswordToggle(buttonId, inputId) {
    const button = document.getElementById(buttonId);
    const input = document.getElementById(inputId);
    if (!button || !input) return;

    button.addEventListener('click', () => {
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.textContent = visible ? 'Tampilkan password' : 'Sembunyikan password';
    });
}

function wireSignupChecklist() {
    const password = document.getElementById('password');
    const submit = document.getElementById('signup-submit');
    if (!password || !submit) return;

    const rules = [
        ['length', value => value.length >= 8],
        ['upper', value => /[A-Z]/.test(value)],
        ['lower', value => /[a-z]/.test(value)],
        ['number', value => /[0-9]/.test(value)],
        ['symbol', value => /[^A-Za-z0-9]/.test(value)]
    ];

    const update = () => {
        let ok = true;
        for (const [id, test] of rules) {
            const item = document.querySelector(`[data-rule="${id}"]`);
            const valid = test(password.value);
            ok = ok && valid;
            if (item) {
                item.className = valid ? 'valid' : 'invalid';
            }
        }
        submit.disabled = !ok;
    };

    password.addEventListener('input', update);
    update();
}

function wireCommentCounter(textareaId, counterId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    if (!textarea || !counter) return;

    const update = () => {
        counter.textContent = String(textarea.value.length);
    };

    textarea.addEventListener('input', update);
    update();
}
