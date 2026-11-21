function validarEntero(entrada, minimo, maximo, nombre = 'n') {
    const texto = (entrada ?? '').trim();

    if (texto === '')
        return {valido: false, mensaje: `Ingrese un valor para ${nombre}.`};

    if (!/^-?\d+$/.test(texto))
        return {valido: false, mensaje: `${nombre} debe ser un número entero (sin decimales, letras ni espacios).`};

    const valor = Number(texto);

    if (valor < minimo || valor > maximo)
        return {valido: false, mensaje: `${nombre} debe estar entre ${minimo} y ${maximo}.`};

    return {valido: true, valor};
}

function iniciarEjercicio({campos, calcular, validar}) {
    const salida = document.getElementById('salida');
    const form = document.getElementById('formulario');
    const inputs = campos.map(c => document.getElementById(c.id));

    function mostrar(texto, esError) {
        salida.className = 'salida visible' + (esError ? ' error' : '');
        salida.textContent = texto;
    }

    function evaluar() {
        const valores = [];
        for (let i = 0; i < campos.length; i++) {
            const r = validarEntero(inputs[i].value, campos[i].min, campos[i].max, campos[i].id);
            if (!r.valido) return mostrar(r.mensaje, true);
            valores.push(r.valor);
        }

        const error = validar && validar(valores);
        if (error) return mostrar(error, true);

        mostrar(calcular(valores));
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        evaluar();
    });

    document.getElementById('limpiar').addEventListener('click', () => {
        form.reset();
        salida.className = 'salida';
        inputs[0].focus();
    });
}

// ejercicio 1
function esPar(n) {
    return n % 2 === 0;
}

function parOImpar(n) {
    return esPar(n) ? 'PAR' : 'IMPAR';
}

// ejercicio #2
function esPrimo(n) {
    if (n < 2) return false;
    for (let i = 2; i <= Math.sqrt(n); i++) {
        if (n % i === 0) return false;
    }
    return true;
}

function primosEnRango(a, b) {
    const primos = [];
    for (let i = a; i <= b; i++) {
        if (esPrimo(i)) {
            primos.push(i);
        }
    }
    return primos;
}

// ejercicio #3
function fibonacci(n) {
    const serie = [0, 1];
    for (let i = 2; i < n; i++) {
        serie.push(serie[i - 1] + serie[i - 2]);
    }
    return serie;
}

function sumaParesFibonacci(serie) {
    let suma = 0;
    for (const termino of serie) {
        if (esPar(termino)) suma += termino;
    }
    return suma;
}
