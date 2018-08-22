<p align="center"><img src="http://eos-mc.dev.gamelogic.com/img/eos-dawn.gif"></p>


## Funder Service

The Funder service is designed to stand up a compact JS-based (React) user interface
that presents a PaySafe-based funding page. The page is used by a logged-in player
to add, remove, alter, and select funding options (credit/debit cards and EFT bank
accounts), then use any of these funding options to perform Wallet funding to the 
eos-wallet service. The paysafe.js library will be used to deal with fields that
may be implicated in PCI compliance.

The majority of code will be React (to build the UI) but there will also be several
Laravel routes to perform essential Wallet interactions. The eos-common WalletService
proxy class will be utilized for all communications to the eos-wallet.
