#!/bin/bash

echo "Updating environment variables..."

source env_vars_list_local.sh

# Create global project env vars file
rm "$ENV_FILE"
touch "$ENV_FILE"

# Check if the .env file exists
if [ -f "$ENV_FILE" ]; then

    ########################################################################
    # App:
    ########################################################################

    declare -a env_variables_app=($(set | grep "^APP_" | cut -d= -f1))

    # Loop to the App update variables in the configuration file
    for var in "${env_variables_app[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Log:
    ########################################################################

    declare -a env_variables_log=($(set | grep "^LOG_" | cut -d= -f1))

    # Loop to update the Log variables in the configuration file
    for var in "${env_variables_log[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Mail:
    ########################################################################

    declare -a env_variables_mail=($(set | grep "^MAIL_" | cut -d= -f1))

    # Loop to update the Mail variables in the configuration file
    for var in "${env_variables_mail[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # DB:
    ########################################################################

    declare -a env_variables_db=($(set | grep "^DB_" | cut -d= -f1))

    # Loop to update the DB variables in the configuration file
    for var in "${env_variables_db[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Rabbit:
    ########################################################################

    declare -a env_variables_rabbit=($(set | grep "^RABBIT_" | cut -d= -f1))

    # Loop to update the Rabbit variables in the configuration file
    for var in "${env_variables_rabbit[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Redis:
    ########################################################################

    declare -a env_variables_redis=($(set | grep "^REDIS_" | cut -d= -f1))

    # Loop to update the Redis variables in the configuration file
    for var in "${env_variables_redis[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Google cloud:
    ########################################################################

    declare -a env_variables_gc=($(set | grep "^GC_" | cut -d= -f1))

    # Loop to update the GC variables in the configuration file
    for var in "${env_variables_gc[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

    echo -e "\n" >> "$ENV_FILE"

    ########################################################################
    # Jenkins:
    ########################################################################

    declare -a env_variables_jenkins=($(set | grep "^JENKINS_" | cut -d= -f1))

    # Loop to update the GC variables in the configuration file
    for var in "${env_variables_jenkins[@]}"; do
        value="${!var}"

        # Check if the variable already exists in the .env file
        if grep -q "^$var=" "$ENV_FILE"; then
            # If it exists, update the value
            awk -v var_name="$var" -v var_value="$value" '{gsub("^"var_name"=.*$", var_name"=" var_value)}1' "$ENV_FILE" > tmpfile && mv tmpfile "$ENV_FILE"
        else
            # If it doesn't exist, add the variable to the end of the file
            echo "$var=$value" >> "$ENV_FILE"
        fi
    done

else
    echo "File not found."
fi

echo "Environment variables loaded successfully."
