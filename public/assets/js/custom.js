// === ALERTA DE CNPJ DUPLICADO ===
function mostrarAlertaDuplicado() {
  const alerta = document.createElement('div');
  alerta.className = 'alerta-duplicado';
  alerta.innerText = '❌ CNPJ já cadastrado no sistema!';
  document.body.appendChild(alerta);
  alerta.style.display = 'block';
  setTimeout(() => alerta.remove(), 4000);
}

// === MENSAGENS DE SUCESSO ===
function mostrarSucesso(msg = "Salvo com sucesso!") {
  const alerta = document.createElement('div');
  alerta.className = 'alerta-duplicado';
  alerta.style.backgroundColor = '#198754';
  alerta.innerText = '✅ ' + msg;
  document.body.appendChild(alerta);
  alerta.style.display = 'block';
  setTimeout(() => alerta.remove(), 4000);
}

// === FORMATAÇÃO AUTOMÁTICA DE MOEDA ===
function formatarMoeda(input) {
  let valor = input.value.replace(/\D/g, '');
  valor = (valor / 100).toFixed(2) + '';
  valor = valor.replace('.', ',');
  valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
  input.value = 'R$ ' + valor;
}

// === TOOLTIP BOOTSTRAP ===
document.addEventListener('DOMContentLoaded', () => {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(t => new bootstrap.Tooltip(t));
});
