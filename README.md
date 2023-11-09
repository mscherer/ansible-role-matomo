Ansible module used to install and upgrade matomo (né piwik)

# Usage

To use it, you just need to add the role to a server
```
$ cat deploy_prosody.yml
- hosts: tracker
  roles:
  - role: matomo
```

