USE siges;
START TRANSACTION;
UPDATE usuarios SET `contraseña` = '$2y$12$Z37k7jR0.ZwOKy2f5oO.yeEEQe5EfeOAygSiw25bOIh73e4Ei6iNq' WHERE correo = 'admin@siges.com' AND BINARY `contraseña` = 'Admin123';
UPDATE usuarios SET `contraseña` = '$2y$12$rFZCNw3e56AOCcMfgnSoO.uoZI2MNeGgoRivoKHsjkeCR7ymNszo6' WHERE correo = 'carlos@siges.com' AND BINARY `contraseña` = 'Carlos123';
UPDATE usuarios SET `contraseña` = '$2y$12$oXPVlog6eT1jS9fcSPxbXu4S/9ORyGwXMEal5ckvi3zV8WhPl/AM.' WHERE correo = 'laura@siges.com' AND BINARY `contraseña` = 'Laura123';
UPDATE usuarios SET `contraseña` = '$2y$12$YskqtTO2PHQ6Sdx2Oog.P.jkrKsWuuZ2Oy8XnAdvMU3tAAVoRq43W' WHERE correo = 'pedro@siges.com' AND BINARY `contraseña` = 'Pedro123';
COMMIT;
