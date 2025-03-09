**Secret-Key Message Project**

This project is a simple Secret-Key Message application was developed by me(Alireza Basirati) using the Laravel framework and MySQL.

**Running and Testing the Project**

- Execute the command: `docker-compose up --build`
- Use the Postman collection file attached to the email

**Project Details**

In the Secret-Key project, depending on the criticality of the issue, different solutions can be chosen for designing the logic and removing expired messages.

1. **Removing Expired Messages**
   - Expired messages are removed when a user references them; if a message has expired, it will be deleted and the user will receive an appropriate response.
   - Two approaches can be used:
     - **Task Scheduler:**  
       This option runs every minute, which means a message may not be deleted at its exact expiration time. It is ideal if you prefer a centralized cleanup process that runs periodically with lower sensitivity.
     - **Delayed Jobs:**  
       This solution is cleaner and more precise, as it deletes each record at its exact expiration time; however, it uses more resources and requires a properly configured queue system.

2. **Design Pattern for Developing the Secret-Key**
   - The current implementation stores the Secret-Key in the database, which is the least secure option but is compatible with the task requirements. (The task description specified that only dependency installation was required, and alternative solutions would necessitate additional OS configuration.)
   - Alternative design patterns for storing the Secret-Key include:
     - **Storing the Secret-Key in a File:**  
       The key can be saved in a file located in a secure directory.
     - **Key Encryption Key (KEK):**  
       A KEK is a cryptographic key used exclusively to encrypt and protect other keys, providing an extra layer of security. In this approach, the primary key is encrypted by another key and stored in the database, while the secondary key is stored in a secure file location.
     - **Cloud Key Management Services (Cloud KMS):**  
       Major cloud providers, such as AWS, Google Cloud, and Azure, offer managed key management services that securely store and manage keys.
