#Release webim

PRIFIX= .
SRC_DIR= ${PRIFIX}
DIST_DIR= ${PRIFIX}/dist
LIB_DIR= ${PRIFIX}/lib
VERSION= 1.1.1
PRODUCT_NAME= PHPWind
CACHE_DIR= ${PRIFIX}/webim
REL_FILE = ${DIST_DIR}/WebIM_For_${PRODUCT_NAME}-${VERSION}.zip
LATE_REL_FILE = ${DIST_DIR}/WebIM_For_${PRODUCT_NAME}-latest.zip
REPLACE_VER= sed s/@VERSION/${VERSION}/

SRC_FILES = ${SRC_DIR}/*.php \
	    ${SRC_DIR}/*.md \
	    ${SRC_DIR}/*.txt \
	    ${SRC_DIR}/*.js \
	    ${SRC_DIR}/lib \
	    ${SRC_DIR}/static \
	    ${SRC_DIR}/admin \
	    ${SRC_DIR}/table \

all: ${REL_FILE}
	@@echo "Build complete."

${REL_FILE}: ${DIST_DIR} ${CACHE_DIR}
	@@echo "Zip ${REL_FILE}"
	@@zip -r -q ${REL_FILE} ${CACHE_DIR}
	@@cp ${REL_FILE} ${LATE_REL_FILE}

${CACHE_DIR}: ${LIB_DIR}/webim.class.php
	@@echo "Create cache directory"
	@@mkdir -p ${CACHE_DIR}
	@@echo "Copy source"
	@@cp -r ${SRC_FILES} ${CACHE_DIR}
	@@rm -rf ${CACHE_DIR}/lib/.git
	@@rm -rf ${CACHE_DIR}/config.php
	@@echo "Change version"
	@@cat ${SRC_DIR}/config_sample.php | ${REPLACE_VER} > ${CACHE_DIR}/config_sample.php
	@@cat ${SRC_DIR}/common.php | ${REPLACE_VER} > ${CACHE_DIR}/common.php

${DIST_DIR}:
	@@echo "Create distribution directory"
	@@mkdir -p ${DIST_DIR}
	@@echo "	"${DIST_DIR}

${LIB_DIR}/webim.class.php:
	@@git submodule update --init lib

clean:
	@@echo "Remove release cache and dist directory"
	@@rm -rf ${DIST_DIR}
	@@rm -rf ${CACHE_DIR}
	@@echo "	"${DIST_DIR}
	@@echo "	"${CACHE_DIR}

