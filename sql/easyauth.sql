--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT 'user',
  `forgot` varchar(255) DEFAULT NULL,
  `remember` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Data for table `users`
--

-- Create one user
-- Login with: 'admin@admin.com' and 'password'
INSERT INTO `users` (`id`, `email`, `password`, `role`, `forgot`, `remember`, `last_login`, `created`) VALUES
(1, 'admin@admin.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin', NULL, NULL, NULL, NOW());
