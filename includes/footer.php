

 </main>
 <footer class="bg-light text-center text-lg-start">
    <div class="text-center p-3">
        © 2025 Syslender
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.js scripts
    var ctxLoan = document.getElementById('loanChart').getContext('2d');
    var loanChart = new Chart(ctxLoan, {
        type: 'line',
        data: {
            // Datos de la gráfica
        }
    });

    var ctxExpense = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctxExpense, {
        type: 'bar',
        data: {
            // Datos de la gráfica
        }
    });
</script>
</body>
</html>
