Testing OCSP with ECDSA certificates on Windows is currently not
possible due to the Windows mongod's inability to load ECDSA
certificates via a PEM file. Once
[SPEC-1589](https://jira.mongodb.org/browse/SPEC-1589) is resolved,
drivers will be able to test against Windows using ECDSA certificates.
