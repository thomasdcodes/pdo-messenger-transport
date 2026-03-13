# 0.1.9
- Fixed "Undefined array key" error in `PdoSender` when using `PhpSerializer` by ensuring headers default to an empty array.

# 0.1.8
- Integrated service registration directly into the Bundle class using `AbstractBundle`. This ensures that the Extension logic is always executed by Symfony, even if naming conventions for separate Extension classes fail.

# 0.1.7
- Forced the PdoTransportFactory service to be public to prevent it from being removed during container optimization.
- Added explicit Tag registration to ensure compatibility with Symfony's Messenger pass.

# 0.1.6
- Switched to manual configuration merging to prevent "Unrecognized option" errors when Symfony's automatic configuration processing is bypassed or fails.

# 0.1.5
- Improved service registration for better autowiring and tagging compatibility.

# 0.1.4
- Fixed Error No transport supports Messenger DSN "pdoqueue://default".
 
# 0.1.0
- Initial bundle setup