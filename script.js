
// script.js - Frontend logic for MiElementos purchases module
function recalcRow(tr) {
  const qty = parseFloat(tr.querySelector('.cantidad').value || 0);
  const vu = parseFloat(tr.querySelector('.valor_unitario').value || 0);
  tr.querySelector('.valor_total').value = (qty * vu).toFixed(2);
}

document.getElementById('addRow').addEventListener('click', () => {
  const tbody = document.querySelector('#productsTable tbody');
  const row = document.createElement('tr');
  row.innerHTML = `
    <td><input name="codigo[]" class="form-control form-control-sm" required></td>
    <td><input name="nombre_producto[]" class="form-control form-control-sm" required></td>
    <td><input name="marca[]" class="form-control form-control-sm"></td>
    <td><input name="cantidad[]" class="form-control form-control-sm cantidad" type="number" step="0.01" required></td>
    <td><input name="valor_unitario[]" class="form-control form-control-sm valor_unitario" type="number" step="0.01" required></td>
    <td><input name="valor_total[]" class="form-control form-control-sm valor_total" readonly></td>
    <td><button type="button" class="btn btn-sm btn-danger removeRow">Eliminar</button></td>`;
  tbody.appendChild(row);
});

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('removeRow')) {
    e.target.closest('tr').remove();
  }
});

document.addEventListener('input', (e) => {
  if (e.target.classList.contains('cantidad') || e.target.classList.contains('valor_unitario')) {
    recalcRow(e.target.closest('tr'));
  }
});

document.getElementById('submitBtn').addEventListener('click', async () => {
  const provDoc = document.getElementById('prov_documento').value.trim();
  const provName = document.getElementById('prov_nombre').value.trim();
  const fecha = document.getElementById('fecha_compra').value;
  if (!provDoc || !provName || !fecha) { alert('Completa los datos obligatorios.'); return; }

  const formData = new FormData();
  formData.append('prov_documento', provDoc);
  formData.append('prov_nombre', provName);
  formData.append('prov_email', document.getElementById('prov_email').value.trim());
  formData.append('fecha_compra', fecha);

  const rows = document.querySelectorAll('#productsTable tbody tr');
  if (rows.length === 0) { alert('Agrega al menos un producto.'); return; }

  rows.forEach(tr => {
    formData.append('codigo[]', tr.querySelector('input[name="codigo[]"]').value);
    formData.append('nombre_producto[]', tr.querySelector('input[name="nombre_producto[]"]').value);
    formData.append('marca[]', tr.querySelector('input[name="marca[]"]').value);
    formData.append('cantidad[]', tr.querySelector('input[name="cantidad[]"]').value);
    formData.append('valor_unitario[]', tr.querySelector('input[name="valor_unitario[]"]').value);
  });

  try {
    const res = await fetch('insertar_compra.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      alert('Compra registrada correctamente.');
      window.location.href = 'reportes.php';
    } else {
      alert('Error: ' + (data.message || 'No se pudo guardar.'));
    }
  } catch (err) {
    alert('Error en la petición: ' + err.message);
  }
});
