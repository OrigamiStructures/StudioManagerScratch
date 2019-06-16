# Studio Manager v 1.0 dev

This is the refactoring of the system that breaks all the edits of the 
original form as it moves to stack data structures rather than the 
Cake-native tree structures.

change the database to stud_m_v1

## Requirements

- Keep records about the work product of Artists. 
- Record the physical nature of the work using any data points the artist desires or just a text description if they want.
- Keep track of all the pieces in any editions.
- Track specific pieces if an edition is numbered
- Track groups of pieces if an edition is not numbered.
- Keep documentary descriptions and photos to explain and identify specific works or specific pieces in an edition.
### Contacts
- Maintain a Rolodex file of People for the registered users
  - Allow multiple addresses for each Person
  - Allow multiple contacts (email, phone, etc) for each Person
- Allow the creation of Institutions in the Rolodex
  - Instututions have addresses and contacts the same as People
- Allow People to be members of any number of Institutions
- Allow Institutions to be members of Institutions
- Allow the the creation of catagorizing labels to organize the Rolodex file
- Allow People and Institutions to be members of any number of Categories
- Allow any Person or Institution to participate in Dispostion Events (*add details below*)
- Allow any Person to be designated as an Artist by the Registered user that created the Person Record
- *What are the deletion rules when an institution is removed?*

