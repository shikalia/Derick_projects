<script>
function showForm(formId) {
document.getElementById('signUpForm').style.display = 'none';
document.getElementById('loginForm').style.display = 'none';
document.getElementById(formId).style.display = 'block';
}

function showSection(sectionId) {
const sections = document.querySelectorAll('.content');
sections.forEach(section => {
section.style.display = 'none';
});
document.getElementById(sectionId).style.display = 'block';
}

function signUp(paymentMethod) {
const form = document.getElementById('signUpFormDetails');
const username = form.username.value;
const password = form.password.value;
const payment = form.payment.value;

alert('Sign-up successful. Payment of 500 confirmed.');
sessionStorage.setItem('username', username);
showAppointmentContent();
} else {
alert('Payment of 500 is required to sign up.');
}
}

function payWithMpesa() {
alert('Redirecting to M-Pesa payment gateway...');

signUp('mpesa');
}

function payWithBank() {
alert('Redirecting to bank payment gateway...');

signUp('bank');
}

function login() {
const form = document.getElementById('loginFormDetails');
const username = form.username.value;
const password = form.password.value;
if (username === sessionStorage.getItem('username')) {
alert('Login successful.');
window.location.href = 'dashboard.html'; 
} else {
alert('Invalid details.');
}
}

function showAppointmentContent() {
document.querySelector('.header').style.display = 'none';
document.getElementById('appointmentContent').style.display = 'block';
}
</script>
