Création d'une interface client pour la réservation de tickets en ligne
======

Technologies utilisées:
----
- Framework Symfony v3, Twig
- Tests unitaires et fonctionnels, PhpUnit
- Solution de paiement Stripe.  
- Materialize css.  


Cahier des charges: 
----
- Design Responsive
- Interface fonctionnelles et rapide
- Contraintes: 
  - Tarif journée, demi journée (à partir de 14h)
  Possibilité de commander pour le jour même (après 14h seul le billet demi journée pourra être réservé)
  - Possibilité de réservation seulement pour les jours définis et ouvert 
  (blocaque des réservation pour les mardi, dimanche, jours fériers, et jours où  plus de 1000 billets ont été vendus)
  - Plusieurs tarifs (normal, enfant, senior, réduit) en fonction de l'âge

- Pour commander il faudra renseigner:
  - Le jour de la visite
  - Le type de billet
  - le nombre de billets souhaités
  
- Pour chaque billet, l'utilisateur doit indiquer son nom, prénom, pays, date de naissance (qui déterminera le tarif du billet)
- Case tarif réduit, 

- Pas nécéssité de création de compte, seul l'email sera utilisé pour envoyer le billet à l'utilisateur

- Solution de paiement par carte bancaire avec la solution Stripe
