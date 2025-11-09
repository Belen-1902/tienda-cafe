document.getElementById('paymentForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  const total = 100; 
  if (!method) {
    alert('Selecciona un método de pago');
    return;
  }

  const response = await fetch('process_payment.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ method, total })
  });

  const data = await response.json();
  alert(data.message || data.error || 'Error desconocido');
});
