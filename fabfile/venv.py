from fabric.api import cd, env, sudo


def _venv(cmd):
    """
    Runs the provided command in a remote virturalenv
    """
    with cd(env.project_dir):
        sudo(
            cmd,
            user=env.app_user
        )
