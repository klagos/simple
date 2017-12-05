INSERT INTO `cuenta` (`id`, `nombre`, `nombre_largo`, `mensaje`, `logo`) VALUES (1, 'default', '', '', NULL);
 
INSERT INTO `usuario_backend` (`id`, `email`, `password`, `nombre`, `apellidos`, `rol`, `salt`, `cuenta_id`, `reset_token`) VALUES (1, 'admin@admin.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Admin', 'Admin', 'super', '', 1, NULL);

INSERT INTO `usuario_manager` (`id`, `usuario`, `password`, `nombre`, `apellidos`, `salt`) VALUES (NULL, 'admin@admin.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Admin', 'Admin', '');
