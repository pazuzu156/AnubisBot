#!/bin/bash
export JAVA_HOME=$(dirname $(dirname $(readlink -f $(which javac))))

function build {
    echo "Building project..."
    mvn install
}

function clean {
    echo "Cleaning project..."
    mvn clean
}

function showhelp {
    echo "Usage: ./build.sh [Option]"
    echo "  -b --build - Build project"
    echo "  -c --clean - Clean project"
    echo "  -h --help  - Show this help message"
}

if [ $# -eq 0 ] ; then
    build
else
    for i in $@
    do
        case $i in
            -b|--build)
                build
                shift
                ;;
            -c|--clean)
                clean
                shift
                ;;
            *)
                showhelp
                shift
                ;;
        esac
    done
fi
