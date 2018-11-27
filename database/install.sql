CREATE TABLE `gatewayMsg` (
  `idGatewayMsg` int(11) NOT NULL,
  `idGateway` varchar(45) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `msgAction` enum('TRANSFER_IN','TRANSFER_OUT') DEFAULT NULL,
  `msgStatus` enum('PENDING','ACK','NACK') DEFAULT NULL,
  `msgDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `msgId` varchar(255) DEFAULT NULL,
  `msg` text NOT NULL,
  `status` enum('ACC','NOT_INTERESTED','PROCESSING') DEFAULT NULL,
  `siInstanceAcc` int(1) DEFAULT NULL ,
  `hrInstanceAcc`int(1) DEFAULT NULL,
  `instance` varchar(5) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Indexes for table `srv_GatewayMsg`
--
ALTER TABLE `gatewayMsg`
  ADD PRIMARY KEY (`idGatewayMsg`),
  ADD KEY `fk_dom_Register_dom_Gateway1_idx` (`idGateway`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `srv_GatewayMsg`
--
ALTER TABLE `gatewayMsg`
  MODIFY `idGatewayMsg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;


CREATE TABLE `gatewayCred` (
  `idGatewayCred` int(11) NOT NULL,
  `idGateway` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `salt` varchar(8) DEFAULT NULL,
  `host` varchar(250) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `transport` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `gatewayCred`
ADD PRIMARY KEY (`idGatewayCred`),
  MODIFY `idGatewayCred` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;


CREATE TABLE `gatewayUser` (
  `idGatewayUser` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(1024) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `gatewayUser`
ADD PRIMARY KEY (`idGatewayUser`),
  MODIFY `idGatewayUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;